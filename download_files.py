import sys
import requests

from config import CCOM_EMAIL

timeframe_start = '2020 01'
timeframe_end = '2024 10'

# Function to extract filename from Content-Disposition header
def get_filename_from_cd(cd):
    if not cd:
        return None
    # Split the header and look for the filename part
    fname = None
    if 'filename=' in cd:
        fname = cd.split('filename=')[-1].strip('"')
    return fname

def download_file(ccom_username, month, year):
    # URL pattern
    url = f'https://api.chess.com/pub/player/{ccom_username}/games/{year}/{month}/pgn'

    # Send a GET request to the URL
    session = requests.session()
    session.headers["User-Agent"] = f"username: {ccom_username}, email: {CCOM_EMAIL}"
    response = session.get(url)

    # Check if the request was successful
    if response.status_code == 200:
        # Try to get the filename from the headers
        filename = get_filename_from_cd(response.headers.get('Content-Disposition'))

        # Prefix file name with directory to save to
        filename = "downloads/" + filename
        
        # Fallback filename if the header doesn't contain one
        if not filename:
            filename = f'{ccom_username}_{year}{month}.pgn'
        
        # Save the file locally
        with open(filename, 'wb') as file:
            file.write(response.content)
        
        print(f"File downloaded successfully and saved as {filename}")
    else:
        print(f"Failed to download file. Status code: {response.status_code}")


def get_all_files(ccom_username):
    curr_year = int(timeframe_start.split()[0])
    curr_month = int(timeframe_start.split()[1])

    end_year = int(timeframe_end.split()[0])
    end_month = int(timeframe_end.split()[1])

    while curr_year < end_year:
        while curr_month <= 12:
            month_str = str(curr_month).zfill(2)
            year_str = str(curr_year)
            download_file(ccom_username, month_str, year_str)
            curr_month += 1

        curr_month = 1
        curr_year += 1
    
    while curr_month <= end_month:
        month_str = str(curr_month).zfill(2)
        year_str = str(curr_year)
        download_file(ccom_username, month_str, year_str)
        curr_month += 1

if __name__ == "__main__":
    # Capture arguments passed from PHP
    ccom_username = sys.argv[1]
    #start_date = sys.argv[2]
    #end_date = sys.argv[3]
    get_all_files(ccom_username)