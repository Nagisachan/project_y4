# -*- coding: utf-8 -*-
import requests
import json
import time

class Tws(object):
    
    def __init__(self):
        self.server_url = "https://internal.insightera.co.th/toolbox/word_segmentation/index.php?method=process"
        requests.packages.urllib3.disable_warnings()
    
    def word_segment(self,sentence):
        print "Request word segmentation...."
        start = time.time()
        r = requests.post(self.server_url, data=('{"text":"' + sentence + '"}').encode('utf-8'),verify=False)
        stop = time.time()
		
        if r.status_code == 200:
            j_output = json.loads(r.text)
            print "Done %d words (%.2fms)" % (len(j_output),(stop-start))
            return j_output
        else:
            print "error", r.status_code, r.reason
            return []
                    
if __name__ == '__main__':
    tws = Tws()
    result = tws.word_segment('ทดสอบตัดคำไทย')
    for word in result:
        print(word)