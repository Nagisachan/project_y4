import urllib2
# If you are using Python 3+, import urllib instead of urllib2

import json
import sys

values = list()
for i in range(1, len(sys.argv)):
    values.append([sys.argv[i], "0"])

data = {
    "Inputs": {
        "input1":
        {
            "ColumnNames": ["text", "tag"],
            "Values": values
        },
    },
    "GlobalParameters": {
    }
}

body = str.encode(json.dumps(data))

url = 'https://ussouthcentral.services.azureml.net/workspaces/f246802ed5b34ff0b72af5a953d0e46a/services/8ee1e141353443419f9e64c58c0f07da/execute?api-version=2.0&details=true'
# Replace this with the API key for the web service
api_key = 'l4ip3lMQvviOlbQPM/T6p1St1FjHK9n7vUDvOFw16q0Fje7GnDeWbFw8ZTaRwHTTNBfXkalFU3m9r2nsuy59bw=='
headers = {
    'Content-Type': 'application/json',
    'Authorization': ('Bearer ' + api_key)
}

req = urllib2.Request(url, body, headers)

try:
    response = urllib2.urlopen(req)
    result = response.read()
    print result
except urllib2.HTTPError, error:
    print "The request failed with status code: " + str(error.code)
    print error.info()
    print json.loads(error.read())
