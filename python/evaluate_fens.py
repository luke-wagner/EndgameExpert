import sys
import asyncio
import json
import time
import aiohttp
import aiomysql
from asyncio import Lock

sys.path.append('../')  # to include python files in the root directory

from config import DB_USERNAME, DB_PWD

# Global lock for ensuring only one request is made at a time
rate_limit_lock = Lock()

async def evaluate_fens(session_id):
    start_time = time.time()

    # Create a connection pool
    pool = await aiomysql.create_pool(
        host="localhost",
        user=DB_USERNAME,
        password=DB_PWD,
        db="chessapp",
        minsize=1,
        maxsize=10  # Adjust based on your needs
    )

    async with pool.acquire() as conn:
        async with conn.cursor() as cursor:
            sql = """
			select f.game_link, f.move_number, f.fen
            from fens f
            inner join (
            select f.game_link, f.piece_count, MAX(move_number) AS move_number
            from game_data gd
            inner join fens f on (f.game_link = gd.game_link)
            left join fen_evals fe on (fe.fen = f.fen)
            where gd.session_id = %s    -- Active session
            and fe.eval is null    -- Don't do evaluation if already done
            group by game_link, piece_count
            ) f2 on (f.game_link = f2.game_link and f.move_number = f2.move_number)
            """
            await cursor.execute(sql, (session_id,))
            results = await cursor.fetchall()

    async with aiohttp.ClientSession() as session:
        for game_link, move_number, fen in results:
            await fetch_and_store_eval(pool, session, game_link, move_number, fen)

    pool.close()
    await pool.wait_closed()

    end_time = time.time()
    print(f"Execution Time: {end_time - start_time:.2f} seconds")


async def fetch_and_store_eval(pool, session, game_link, move_number, fen):
    retries = 5
    while retries > 0:
        try:
            async with rate_limit_lock:  # Ensure only one request at a time
                async with session.get(f'http://tablebase.lichess.ovh/standard?fen={fen}') as response:
                    if response.status == 429:
                        print("Rate limit hit. Retrying in 60 seconds...")
                        await asyncio.sleep(60)  # Wait for rate limit to reset
                        continue
                    elif response.status == 200:
                        data = await response.json()
                        eval_category = data.get('category')

                        eval_num = {
                            'win': 1,
                            'loss': -1,
                            'draw': 0
                        }.get(eval_category, None)

                        if eval_num is not None:
                            async with pool.acquire() as conn:
                                async with conn.cursor() as cursor:
                                    sql = "INSERT INTO fen_evals (fen, eval) VALUES (%s, %s)"
                                    val = (fen, eval_num)
                                    await cursor.execute(sql, val)
                                    await conn.commit()

                        print(f"Game Link: {game_link}, Move Number: {move_number}, FEN: {fen}, Eval: {eval_num}")
                        return  # Successfully processed, exit the retry loop
                    else:
                        print(f"Unexpected status {response.status} for FEN: {fen}")
                        return  # Do not retry non-rate-limiting errors
        except Exception as e:
            print(f"Error processing FEN: {fen} - {e}")
        retries -= 1
        if retries > 0:
            print(f"Retrying FEN: {fen} ({retries} retries left)...")
            await asyncio.sleep(5)  # Wait before retrying
        else:
            print(f"Failed to process FEN after multiple retries: {fen}")


if __name__ == "__main__":
    #asyncio.run(evaluate_fens(131)) # Function call for testing purposes

    # Capture arguments passed from PHP
    session_id = sys.argv[1]
    asyncio.run(evaluate_fens(session_id))