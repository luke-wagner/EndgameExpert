import mysql.connector
import re
import sys

sys.path.append('../') # to include python files in the root directory

import dependencies.pgnToFen.pgntofen as ptf
from categorize_pos import *

from config import DB_USERNAME, DB_PWD

db = mysql.connector.connect(
  host="localhost",
  user=DB_USERNAME,
  password=DB_PWD,
  database="chessapp"
)

cursor = db.cursor()

def categorize_game(pgn, player_color, game_link):
    pgnConverter = ptf.PgnToFen()
    pgnConverter.resetBoard()
    stats = pgnConverter.parsePgnPlainText(pgn)

    for game_data in stats['succeeded']:
        fens = game_data[1]

        move_number = 0
        last_fen = ''
        for fen in fens:
            piece_data = fen.split()[0]
            num_pieces = len(re.findall('[a-zA-Z]', piece_data))
            descriptor = ''

            if num_pieces > 7:
                continue

            descriptor = categorize_pos(player_color, fen)
            
            # Insert into fens table
            sql = "INSERT INTO fens (game_link, move_number, piece_count, fen, descriptor) VALUES (%s, %s, %s, %s, %s)"
            val = (game_link, move_number, num_pieces, fen, descriptor)
            try:
                cursor.execute(sql, val)
                db.commit()
            except:
                pass
            
            if fen != last_fen:
                move_number += 1
            
            last_fen = fen