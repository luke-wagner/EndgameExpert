import mysql.connector
import requests
import json
from tqdm.asyncio import tqdm
from concurrent.futures import ThreadPoolExecutor, as_completed
import time

from config import DB_USERNAME, DB_PWD

db = mysql.connector.connect(
  host="localhost",
  user=DB_USERNAME,
  password=DB_PWD,
  database="chessapp"
)

cursor = db.cursor()

def process_row(row):
    local_conn = mysql.connector.connect(
        host="localhost",
        user=DB_USERNAME,
        password=DB_PWD,
        database="chessapp"
    )

    local_cursor = db.cursor()

    fen = row[0]

    fen_formatted = fen.replace(' ', '_')

    r = requests.get('http://tablebase.lichess.ovh/standard?fen=' + fen_formatted)

    try:
        data = json.loads(r.content)

        eval = data['category']

        if eval == 'win':
            eval_num = 1
        elif eval == 'loss':
            eval_num = -1
        elif eval == 'draw':
            eval_num = 0
        else:
            print("Special case detected")
            raise Exception

        # Insert into evals table
        sql = "INSERT INTO evals (fen, eval) VALUES (%s, %s)"
        val = (fen, eval_num)
        try:
            local_cursor.execute(sql, val)
        except:
            pass

    except:
        pass

    local_conn.commit()
    local_cursor.close()
    local_conn.close()

def process_rows_in_parallel(rows, max_workers=10):
    with ThreadPoolExecutor(max_workers=max_workers) as executor:
        futures = [executor.submit(process_row, row) for row in rows]
        for future in as_completed(futures):
            result = future.result()  # Get result if needed, or just process exceptions


cursor.execute("SELECT distinct fen FROM fens")

result = cursor.fetchall()

start_time = time.time()
process_rows_in_parallel(result, max_workers=10)  # Adjust workers based on your system
end_time = time.time()

print(f"Processing completed in {end_time - start_time} seconds.")




