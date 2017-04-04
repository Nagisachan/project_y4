import urllib2
# If you are using Python 3+, import urllib instead of urllib2

import json
import sys

values = list()
for i in range(3, len(sys.argv)):
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

url = sys.argv[1]
api_key = sys.argv[2]
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
