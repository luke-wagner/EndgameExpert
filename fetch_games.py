import sys
from datetime import datetime

import data_request as dr

if __name__ == "__main__":
    # Capture arguments passed from PHP
    ccom_username = sys.argv[1]
    start_date = sys.argv[2]
    end_date = sys.argv[3]
    
    print("Username: " + ccom_username)
    print("Start date: " + start_date)
    print("End date: " + end_date)

    # Iterate over all month date combinations possible in this timeframe
    start = datetime.strptime(start_date, "%Y-%m-%d")
    end = datetime.strptime(end_date, "%Y-%m-%d")

    current = start
    while current <= end:
        month_str = str(current.month).zfill(2)
        year_str = str(current.year)

        # Construct API call
        url = f"https://api.chess.com/pub/player/{ccom_username}/games/{year_str}/{month_str}"
        print("Sending request: " + url)
        response = dr.send_request(url)        
        print(response)

        # Increment by one month
        if current.month == 12:
            current = current.replace(year=current.year + 1, month=1)
        else:
            current = current.replace(month=current.month + 1)