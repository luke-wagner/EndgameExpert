#####################################################################################
# File: data_request.py
#
# Defines the method send_request, for making an API call and receiving back data
#####################################################################################

import requests
import json

from config import USING_PROXY, CCOM_USERNAME, CCOM_EMAIL

proxy_url = "http://localhost:5000/fetch" # The DigitalOcean droplet uses a proxy for getting the data
headers =  {'User-Agent': f'username: {CCOM_USERNAME}, email: {CCOM_EMAIL}', 'Accept-Encoding': 'gzip, deflate',
             'Accept': '*/*', 'Connection': 'keep-alive'} # Include these headers for the request

# Make an API call to the requested url and return the json data
def send_request(url):
    if USING_PROXY == True:
        # Construct the payload
        payload = {"url": url, "headers": headers}

        try:
            # Send a request to the proxy, receive data back
            response = requests.post(proxy_url, json=payload)

            if response.status_code == 200:
                json_str = response.json()['data'] # when sent through the proxy, the json data becomes a string
                return json.loads(json_str)
            else:
                print("Error:", response.text)
                return []
        except Exception as e:
            print("Failed to fetch data:", str(e))
            return []
    else:
        # Send a GET request to the URL
        session = requests.session()
        session.headers = headers
        response = session.get(url)

        if response.status_code == 200:
            return response.json()
        else:
            print(f"Failed to fetch data: {response.status_code}")
            return []

if __name__ == "__main__":
    result = send_request("https://api.chess.com/pub/player/Hikaru") # Example API call
    print(result)
