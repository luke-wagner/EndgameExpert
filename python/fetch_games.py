import sys
from datetime import datetime
import mysql.connector

sys.path.append('../') # to include python files in the root directory

from config import DB_USERNAME, DB_PWD
import data_request as dr
from categorize_game import categorize_game

db = mysql.connector.connect(
  host="localhost",
  user=DB_USERNAME,
  password=DB_PWD,
  database="chessapp"
)

cursor = db.cursor()

# session_data table gives a way to track which data has been fetched for the active session
def insert_session_data(session_id, username, year, month):
    # Insert into session_data table
    sql = """
    INSERT INTO session_data
    (session_id, username, month_str, year_str)
    VALUES (%s, %s, %s, %s)
    """
    vals = (session_id, username, month, year)
    try:
        cursor.execute(sql, vals)
        db.commit()
    except Exception as e:
        sys.stderr.write(str(e))

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
def parse_month_data(json, session_id, user_player_name, month, year):
    for game in json["games"]:
        game_link = game["url"]
        pgn = game["pgn"]
        white_player = game["white"]["username"]
        black_player = game["black"]["username"]
        player_color = 'W' if user_player_name == white_player else 'B'
        opponent_name = black_player if player_color == 'W' else white_player
        user_player_data = game["white"] if player_color == 'W' else game["black"]
        opponent_player_data = game["black"] if player_color == 'W' else game["white"]
        opponent_rating = opponent_player_data["rating"]
        game_result = user_player_data["result"]
        outcome = get_outcome(game_result)

        # Insert into game_data table
        sql = """
        INSERT INTO game_data 
        (session_id, player_name, game_link, player_color, opponent_name, opponent_rating, outcome, result, month, year) 
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE player_name = player_name, opponent_name = opponent_name, game_link = game_link;
        """
        vals = (session_id, user_player_name, game_link, player_color, opponent_name, opponent_rating, outcome, game_result, month, year)
        try:
            cursor.execute(sql, vals)
            db.commit()
        except Exception as e:
            sys.stderr.write(str(e))

        # Insert into fens table
        categorize_game(pgn, player_color, game_link)

        # Evaluate game
        # TODO: Implement evaluation of games
        # started in evaluate_fens.py

if __name__ == "__main__":
    # Capture arguments passed from PHP
    session_id = sys.argv[1]
    ccom_username = sys.argv[2]
    start_date = sys.argv[3]
    end_date = sys.argv[4]

    # Iterate over all month date combinations possible in this timeframe
    start = datetime.strptime(start_date, "%Y-%m-%d")
    end = datetime.strptime(end_date, "%Y-%m-%d")

    current = start
    while current <= end:
        month_str = str(current.month).zfill(2)
        year_str = str(current.year)

        # Check if games were already fetched for this month/year in the active session
        sql = f"""
        SELECT COUNT(*) FROM session_data
        WHERE session_id = {session_id}
        AND username = '{ccom_username}'
        AND year_str = '{year_str}' AND month_str = '{month_str}'
        """
        try:
            cursor.execute(sql)
            num_occurences = cursor.fetchone()[0]
        except Exception as e:
            sys.stderr.write(str(e))

        if num_occurences < 1:  # Data not yet fetched for this user and this month/year
            insert_session_data(session_id, ccom_username, year_str, month_str)

            # Construct API call - use monthly archives
            url = f"https://api.chess.com/pub/player/{ccom_username}/games/{year_str}/{month_str}"
            print("Sent request: " + url)
            json_data = dr.send_request(url)

            # Parse data and insert into game_data        
            parse_month_data(json_data, session_id, ccom_username, month_str, year_str)      
        else:
            print("API call not necessary")

        # Increment by one month
        if current.month == 12:
            current = current.replace(year=current.year + 1, month=1)
        else:
            current = current.replace(month=current.month + 1)