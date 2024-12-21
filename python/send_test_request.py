#####################################################################################
# File: send_test_request.py
#
# Simple script to test sending an API call and printing the returned data
#####################################################################################

import data_request as dr

result = dr.send_request("https://api.chess.com/pub/player/Hikaru")
print(result)