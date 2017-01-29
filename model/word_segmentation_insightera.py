# -*- coding: utf-8 -*-
import requests
import json
import time
import sys

class Tws(object):
    
    def __init__(self):
        #self.server_url = "https://internal.insightera.co.th/toolbox/word_segmentation/index.php?method=process"
        self.server_url = "http://punyapat.org/web/tech/lexto/ws.php"
        requests.packages.urllib3.disable_warnings()
    
    def word_segment(self,sentence):
        # print "Request word segmentation...."
        sentence = sentence.replace('"',"'")
        start = time.time()
        r = requests.post(self.server_url, data=('{"text":"' + sentence + '"}').encode('utf-8'),verify=False)
        stop = time.time()
		
        if r.status_code == 200:
            j_output = json.loads(r.text)
            #print "Done %5d words (%.2fms)" % (len(j_output),(stop-start))
            sys.stdout.write('.')
            sys.stdout.flush()
            return j_output
        else:
            print "error input=%s status=%d reason=%s" % (sentence, r.status_code, r.reason)
            return []
                    
if __name__ == '__main__':
    tws = Tws()
    result = tws.word_segment(unicode('ทดสอบตัดคำไทยดูว่าแม่นไหม','utf-8'))
    for word in result:
        print(word)