import sys
import mysql.connector
import json
import requests

sys.path.append('../') # to include python files in the root directory

from config import DB_USERNAME, DB_PWD

db = mysql.connector.connect(
  host="localhost",
  user=DB_USERNAME,
  password=DB_PWD,
  database="chessapp"
)

cursor = db.cursor()

def evaluate_fens(session_id, username, year, month):
    sql = """
    select f.game_link, f.move_number, f.fen
    from game_data gd
    inner join fens f on (f.game_link = gd.game_link)
    where gd.session_id = %s and player_name = %s and gd.month = %s and gd.year = %s
    """
    vals = (session_id, username, month, year)
    cursor.execute(sql, vals)
    results = cursor.fetchall()
    
    for row in results:
        game_link, move_number, fen = row

        response = requests.get('http://tablebase.lichess.ovh/standard?fen=' + fen)

        try:
            data = json.loads(response.content)

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
            sql = "INSERT INTO fen_evals (fen, eval) VALUES (%s, %s)"
            val = (fen, eval_num)
            try:
                cursor.execute(sql, val)
                db.commit()
            except Exception as e:
                print(e)
        except Exception as e:
            print(e)

        print(f"Game Link: {game_link}, Move Number: {move_number}, FEN: {fen}", "Eval: ", eval_num)

if __name__ == "__main__":

    db.close()