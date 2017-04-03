import urllib2
# If you are using Python 3+, import urllib instead of urllib2

import json


data = {

    "Inputs": {

        "text":
        {
            "ColumnNames": ["text", "tag"],
            "Values": [["value", "0"], ["value", "0"], ]
        }, },
    "GlobalParameters": {
    }
}

body = str.encode(json.dumps(data))

url = 'https://ussouthcentral.services.azureml.net/workspaces/f246802ed5b34ff0b72af5a953d0e46a/services/fbcdcbbf26b14d659c042f96038d298e/execute?api-version=2.0&details=true'
api_key = '8NRdgFZYAcbuKm+17Z8Iel5wP3gjLw4pFvD+oxl25NY1oF0cCeZt6lnm7sCUHNZv1vNj/gB1taOF/CdelKV3fA=='  # Replace this with the API key for the web service
headers = {'Content-Type': 'application/json',
           'Authorization': ('Bearer ' + api_key)}

req = urllib2.Request(url, body, headers)

try:
    response = urllib2.urlopen(req)

    # If you are using Python 3+, replace urllib2 with urllib.request in the above code:
    # req = urllib.request.Request(url, body, headers)
    # response = urllib.request.urlopen(req)

    result = response.read()
    print(result)
except urllib2.HTTPError, error:
    print("The request failed with status code: " + str(error.code))

    # Print the headers - they include the requert ID and the timestamp, which
    # are useful for debugging the failure
    print(error.info())

    print(json.loads(error.read()))
