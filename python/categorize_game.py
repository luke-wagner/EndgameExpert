import mysql.connector
import re
import sys
from datetime import datetime

sys.path.append('../') # to include python files in the root directory

import dependencies.pgnToFen.pgntofen as ptf
from categorize_pos import *

from config import DB_USERNAME, DB_PWD

ERR_FILE_PATH = '../out/categorize_game.err'

db = mysql.connector.connect(
  host="localhost",
  user=DB_USERNAME,
  password=DB_PWD,
  database="chessapp"
)

cursor = db.cursor()

# Gets all fens for a game (from a pgn)
# Categorizes and inserts into fens table
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

    # When pgn to fen conversion fails, write errors to out file
    for game_data in stats['failed']:
        timestamp = datetime.now().strftime("%Y-%m-%dT%H:%M:%S.%f")
        timestamp = '( ' + timestamp + ' )\n'
        file_lines = [timestamp, game_link + '\n']
        for elem in game_data:
            file_lines.append(str(elem) + '\n')
        file_lines.append('\n\n')

        with open(ERR_FILE_PATH, 'a') as f:
            f.writelines(file_lines)