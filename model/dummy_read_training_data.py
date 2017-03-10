import os
from read_training_data import N
from word_segmentation_insightera import Tws

class DummyN(object):
    def __init__(self):
        self.cache_file_text = 'dummy-input-text.txt'
        self.cache_file_tag = 'dummy-input-tag.txt'

    def read_text_tag(self):
        print "[Fetch Data] read dummy, pre-wordsegmented text"
        if os.path.isfile(self.cache_file_text) and os.path.isfile(self.cache_file_tag):
            text=[]
            with open(self.cache_file_text,'r') as f:
                for line in f:
                    text.append(unicode(line,"utf-8"))

            tag=[]
            with open(self.cache_file_tag,'r') as f:
                for line in f:
                    tag.append(unicode(line,"utf-8"))

            return ['0-0' for i in range(0,len(text))],text,tag

    def rewrite_cache(self):
        text,tag = N().read_text_tag()
        tws = Tws()

        with open(self.cache_file_text,'w') as f:
            for t in text:
                f.write(';'.join(tws.word_segment(t.strip())).encode('UTF-8') + '\n')
        with open(self.cache_file_tag,'w') as f:
            for t in tag:
                f.write(t.encode('utf-8') + '\n')

if __name__ == '__main__':
    DummyN().rewrite_cache()
