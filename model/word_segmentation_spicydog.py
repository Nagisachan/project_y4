# -*- coding: utf-8 -*-
import requests
import json

class Tws(object):
    
    def __init__(self):
        self.server_url = "https://tws.spicydog.org/php/api.php?action=segment"
    
    def word_segment(self,sentence):
        r = requests.post(self.server_url, data={'text':sentence})

        if r.status_code == 200:
            j_output = json.loads(r.text)
            return j_output['output'].split('|')
        else:
            print "error", r.reason
            return []
                    
if __name__ == '__main__':
    tws = Tws()
    result = tws.word_segment('ทดสอบตัดคำไทย')
    for word in result:
        print word