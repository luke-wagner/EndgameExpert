import mysql.connector

from config import DB_USERNAME, DB_PWD

db = mysql.connector.connect(
  host="localhost",
  user=DB_USERNAME,
  password=DB_PWD,
  database="chessapp"
)

cursor = db.cursor()

# Delete from fens
sql = "delete from fens"
try:
    cursor.execute(sql)
    db.commit()
except:
    pass

# Delete from game_data
sql = "delete from game_data"
try:
    cursor.execute(sql)
    db.commit()
except:
    pass