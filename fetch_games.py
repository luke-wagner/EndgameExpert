import sys
from datetime import datetime
import mysql.connector

from config import DB_USERNAME, DB_PWD
import data_request as dr

db = mysql.connector.connect(
  host="localhost",
  user=DB_USERNAME,
  password=DB_PWD,
  database="chessapp"
)

cursor = db.cursor()

# Return outcome as int from result string
# Listing of game result codes at https://www.chess.com/news/view/published-data-api#game-results
# Return 1 for win, -1 for loss, and 0 for draw
def get_outcome(result):
    if result == "win":
        return 1
    elif result == "lose" or result == "checkmated" or result == "timeout" or result == "resigned" or result == "abandoned":
        return -1
    elif result == "agreed" or result == "repetition" or result == "stalemate" or result == "insufficient" or result == "50move" or result == "timevsinsufficient":
        return 0

# Parse data from monthly archive and insert into game_data
def parse_month_data(json, user_player_name, month, year):
    for game in json["games"]:
        game_link = game["url"]
        white_player = game["white"]["username"]
        black_player = game["black"]["username"]
        player_color = 'W' if user_player_name == white_player else 'B'
        user_player_data = game["white"] if player_color == 'W' else game["black"]
        game_result = user_player_data["result"]
        outcome = get_outcome(game_result)

        # Insert into game_data table
        sql = "INSERT INTO game_data (game_link, player_color, outcome, result, month, year) VALUES (%s, %s, %s, %s, %s, %s)"
        vals = (game_link, player_color, outcome, game_result, month, year)
        try:
            cursor.execute(sql, vals)
            db.commit()
        except:
            pass

        # Insert into fens table
        # Placeholder data for now
        final_fen = game["fen"]
        sql = "INSERT INTO fens (game_link, move_number, fen, descriptor) VALUES (%s, %s, %s, %s)"
        vals = (game_link, 0, final_fen, 'QvR')
        try:
            cursor.execute(sql, vals)
            db.commit()
        except:
            pass

if __name__ == "__main__":
    # Capture arguments passed from PHP
    ccom_username = sys.argv[1]
    start_date = sys.argv[2]
    end_date = sys.argv[3]

    # Iterate over all month date combinations possible in this timeframe
    start = datetime.strptime(start_date, "%Y-%m-%d")
    end = datetime.strptime(end_date, "%Y-%m-%d")

    current = start
    while current <= end:
        month_str = str(current.month).zfill(2)
        year_str = str(current.year)

        # Construct API call - use monthly archives
        url = f"https://api.chess.com/pub/player/{ccom_username}/games/{year_str}/{month_str}"
        print("Sent request: " + url)
        json_data = dr.send_request(url)        
        parse_month_data(json_data, ccom_username, month_str, year_str)

        # Increment by one month
        if current.month == 12:
            current = current.replace(year=current.year + 1, month=1)
        else:
            current = current.replace(month=current.month + 1)