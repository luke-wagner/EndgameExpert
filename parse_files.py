import sys

import dependencies.pgnToFen.pgntofen as ptf
import re
import os
import mysql.connector

from config import DB_USERNAME, DB_PWD
from categorize_pos import categorize_pos

db = mysql.connector.connect(
  host="localhost",
  user=DB_USERNAME,
  password=DB_PWD,
  database="chessapp"
)

cursor = db.cursor()

def parseFile(fileName, ccom_username):
    pgnConverter = ptf.PgnToFen()
    pgnConverter.resetBoard()
    stats =  pgnConverter.pgnFile(fileName)

    for game_data in stats['succeeded']:
        game_link = game_data[0][-1]
        game_link = re.findall(r'"(.*?)"', game_link)[0]

        player_color_index = str(game_data[0]).find(ccom_username) - 7
        player_color = str(game_data[0])[player_color_index]

        result = game_data[0][6].strip()
        
        if result == '[Result "1-0"]':
            if player_color == 'W':
                outcome = 1
            else:
                outcome = -1
        elif result == '[Result "0-1"]':
            if player_color == 'B':
                outcome = 1
            else:
                outcome = -1
        elif result == '[Result "1/2-1/2"]':
            outcome = 0
        else:
            # We have a problem
            print("YIKES!")
            print(result)

        # Insert into game_data table
        sql = "INSERT INTO game_data (game_link, player_color, outcome) VALUES (%s, %s, %s)"
        val = (game_link, player_color, outcome)
        try:
            cursor.execute(sql, val)
            db.commit()
        except:
            pass

        fens = game_data[1]

        move_number = 0
        last_fen = ''
        for fen in fens:
            player = fen.split()[1]
            piece_data = fen.split()[0]
            num_pieces = len(re.findall('[a-zA-Z]', piece_data))
            descriptor = ''

            if num_pieces > 7:
                continue

            pieces = get_pieces(fen)
            descriptor = categorize_pos(player_color, pieces)
            
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

def parseAllFiles(ccom_username):
    pgnFiles = os.listdir('./downloads')
    
    for pgnFile in pgnFiles:
        try:
            parseFile("downloads/" + pgnFile, ccom_username)
        except:
            pass

def get_pieces(fen):
    pieces = fen.split()[0]
    pieces = re.findall('[a-z,A-Z]', pieces)
    pieces.sort()
    return pieces


if __name__ == "__main__":
    # Capture arguments passed from PHP
    ccom_username = sys.argv[1]
    #start_date = sys.argv[2]
    #end_date = sys.argv[3]
    parseAllFiles(ccom_username)