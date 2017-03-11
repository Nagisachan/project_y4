# -*- encoding: utf-8 -*-

from dummy_read_training_data import DummyN
from word_segmentation_insightera import Tws
from sklearn.feature_extraction.text import CountVectorizer
from read_training_data import N
import numpy as np
import sys
import string
import codecs
import operator
import random

class RawData(object):
    def __init__(self):
        #self.read = N()
        self.read = DummyN()
        #self.read =ImpalaDB()
        self.tws = Tws()
        self.train_text_ratio = 0.8
        self.tag_table = {'OTHER':0}
        self.tag_inverse_table = {0:'OTHER'}
        self.raw_paragraph_text = []
        self.raw_paragraph_tag = []
        self.is_inited = False
        self.test_text_id = False
        self.test_text_result = False
        
        # read stop words list
        stopwords = codecs.open('stop_words.txt', 'r','utf-8').read().split()
        self.stopwords = stopwords
        
        for stopword in stopwords:
            print stopword.encode('utf-8'),
        print
        print "[Preprocess] all stopword = %d words" % len(stopwords)
        
        # read lemma dict
        lemma_dict = dict()
        with open('lemma_dict','r') as f:
            for line in f:
                lemma,words = line.split(":")
                for word in words.split(","):
                    word = word.strip()
                    lemma_dict[unicode(word,'utf-8')] = lemma
                    
                    print "%s => %s" % (word,lemma)
        
        self.lemma_dict = lemma_dict
        print "[Preprocess] all lemma = %d rows" % len(lemma_dict)
        
    def load(self,sample_n=0,is_verbose=False):
        # 1. word segmentation
        # 2. remove punctuation
        # 3. remove stop words
        # 4. convert tag to ID
        # 5. create doc-tag(s) style

        text = []
        tag = []

        # read raw data
        text_id,text,tag = self.read.read_text_tag()
        
        if sample_n > 0:
            text_id = text_id[:sample_n]
            text = text[:sample_n]
            tag = tag[:sample_n]
            
        print "[Preprocess]: fetch from %d paragraphs" % len(tag)
        
        # create 1 tag per doc raw data
        new_text = []
        new_tag = []
        
        lemma_count = 0
        stopword_count = 0
        for i in range(0,len(text)):
            filteredtext = []
            #tmp_text = self.tws.word_segment(unicode(text[i].strip(),'utf-8'))

            # use dummy input which has already been segmented separate by ';' 
            tmp_text = text[i].split(';')

            # preprocess
            for t in tmp_text:
                t = t.strip()
                
                # remove punctuation
                t = t.translate({ord(char): None for char in (string.punctuation + unicode('‘’“”…๑๒๓๔๕๖๗๘๙๐','utf-8'))})
                
                #Lemmatization
                if t in self.lemma_dict:
                    lemma_count += 1
                    if is_verbose:
                        print "change %s => %s" % (t.encode('utf-8'),lemma_dict[t])
                  
                # remove stop word
                if t not in self.stopwords and t.strip():
                    filteredtext.append(t)
                else:
                    stopword_count += 1
                    if is_verbose:
                        print "remove %s" % (t)
            
            # we will do word segmentation using only a space.            
            filteredtext = ' '.join([l for l in filteredtext])
            
            tmp_tag = tag[i].split(',')
            tmp_tag = [t.strip() for t in tmp_tag]
            
            all_tag = []
            for t in tmp_tag:
                if t == '':
                    continue;
            
                """
                # convert tag to int
                if t not in self.tag_table:
                    idx = len(self.tag_table)
                    self.tag_table[t] = idx
                    self.tag_inverse_table[idx] = t
                else:
                    idx = self.tag_table[t];
                """

                idx = int(t)
                all_tag.append(idx)
            
            self.raw_paragraph_text.append((text_id[i],filteredtext)) #text
            self.raw_paragraph_tag.append(all_tag) #tag ID
        
        print
        print "[Preprocess]: remove punctuation %s" % (string.punctuation + '‘’“”…๑๒๓๔๕๖๗๘๙๐')
        print "[Preprocess]: apply lemma = %d pairs" % lemma_count
        print "[Preprocess]: remove stopword = %d words" % stopword_count
        
        # random sample order
        text = []
        tag = []
        m = len(self.raw_paragraph_tag)
        rand_index = random.sample(range(m),m)
        for idx in rand_index:
            text.append(self.raw_paragraph_text[idx])
            tag.append(self.raw_paragraph_tag[idx])
                        
        self.raw_paragraph_text = text
        self.raw_paragraph_tag = tag
    
        #for tag in self.read.get_all_tag():
        #    self.tag_table[unicode(tag[1],'utf-8')] = tag[0];
        #    self.tag_inverse_table[tag[0]] = unicode(tag[1],'utf-8');
        
        self.is_inited = True;
    def get_all_text(self):
        return self.raw_paragraph_text
    
    def get_train_test_data_tag(self,tag_idx,is_verbose=False):
        # 1. create 2 classes match and not match
        # 2. make classes balance (randomly select same number of sample from 2 classes)
        # 3. select train/test sample data count
        # 4. assignment a tag to each doc, tag ID (non-zero) and zero
        # 5. Zipf’s rule

        #print "Get tag %s(%d) = %d" % (self.tag_inverse_table[tag_idx],tag_idx,[tag_idx in b for b in self.raw_paragraph_tag].count(True))
        match_text = []
        not_match_text = []
        
        # separate in to 2 classes
        for idx in range(0,len(self.raw_paragraph_tag)):
            if tag_idx in self.raw_paragraph_tag[idx]:
                match_text.append(self.raw_paragraph_text[idx])
            else:
                not_match_text.append(self.raw_paragraph_text[idx])

        # select equal number of class
        if len(not_match_text) == len(match_text):
            pass
        if len(not_match_text) > len(match_text):
            random.shuffle(not_match_text)
            not_match_text = not_match_text[0:len(match_text)]
        else:
            random.shuffle(match_text)
            match_text = match_text[0:len(not_match_text)]
        
        # train/test sample count
        train_data_count = int(len(match_text)*self.train_text_ratio)
        
        test_match_text = match_text[train_data_count:]
        test_not_match_text = not_match_text[train_data_count:]
        
        train_match_text = match_text[:train_data_count]
        train_not_match_text = not_match_text[:train_data_count]
        
        # random test sample
        all_text = test_match_text + test_not_match_text
        all_tag = [tag_idx for i in range(len(test_match_text))] + [0 for i in range(len(test_not_match_text))]
        
        result_tag = []
        result_text = []
        rand_index = random.sample(range(len(all_tag)),len(all_tag))
        
        for idx in rand_index:
            result_text.append(all_text[idx])
            result_tag.append(all_tag[idx])
        
        test_text = result_text
        test_tag = result_tag
        
        # random train sample
        all_text = train_match_text + train_not_match_text
        all_tag = [tag_idx for i in range(0,len(train_match_text))] + [0 for i in range(0,len(train_not_match_text))]
        
        result_tag = []
        result_text = []
        rand_index = random.sample(range(len(all_tag)),len(all_tag))
        
        for idx in rand_index:
            result_text.append(all_text[idx])
            result_tag.append(all_tag[idx])
        
        train_text = result_text
        train_tag = result_tag
    
        # Zipf’s rule
        from collections import defaultdict
        frequency = defaultdict(int) # default = 0

        for text in train_text:
            words = text[1].split(' ')
            for token in words:
                frequency[token] += 1
        
        #FIXME
        all_word_count = len(frequency.keys())
        all_word_occur_count = sum(frequency.values())
        min_threshold = int(0.005*all_word_count)
        max_threshold = int(0.6*all_word_count)
        sorted_frequency = sorted(frequency.items(), key=operator.itemgetter(1), reverse=True)
        
        if is_verbose:
            for fword,fvalue in sorted_frequency[:10] + sorted_frequency[-5:]:
                print fword,fvalue
        
        all_filtered_word_occur_count = 0
        for i in range(len(train_text)):
            words = train_text[i][1].split(' ')
            tmp = [word for word in words if frequency[word] <= max_threshold and frequency[word] >= min_threshold]
            all_filtered_word_occur_count += len(tmp)
            train_text[i] = (train_text[i][0]," ".join(tmp)) 

        #print "min=%d max=%d before=%d/%d after=%d" % (min_threshold,max_threshold,all_word_occur_count,all_word_count,all_filtered_word_occur_count)
        
        return train_text,train_tag,test_text,test_tag
        
    def get_target_names(self):
        tmp = []
        if self.is_inited:
            for i in range(0,len(self.tag_inverse_table)):
                tmp.append(self.tag_inverse_table[i])
        else:
            for tag in self.read.get_all_tag():
                tmp.append(tag[1])
                
        return tmp
        
    def show_tag_summary(self):
        summary = []
        for name, idx in self.tag_table.iteritems():
            summary.append(("%s(%d)" % (name,idx),[idx in t for t in self.raw_paragraph_tag].count(True)))
            
        sorted_summary= sorted(summary, key=operator.itemgetter(1), reverse=True)
        for tag, count in sorted_summary:
            print "%5d %s" % (count,tag)
    
    def get_all_tag_idx(self):
        ret = []
        if self.is_inited:
            for tag in self.tag_inverse_table.iterkeys():
                ret.append(tag)
        else:
            for tag in self.read.get_all_tag():
                ret.append(tag[0])
                
        return ret
    
    def load_test_text(self):
        text_id, text = self.read.read_test_text()
        text_result = [];
        
        lemma_count = 0
        stopword_count = 0
        
        print "[[Preprocess]: process %d text" % len(text)
        for i in range(0,len(text)):
            filteredtext = []
            tmp_text = self.tws.word_segment(unicode(text[i].strip(),'utf-8'))

            # use dummy input which has already been segmented separate by ';' 
            #tmp_text = text[i].split(';')

            # preprocess
            for t in tmp_text:
                t = t.strip()
                
                # remove punctuation
                t = t.translate({ord(char): None for char in (string.punctuation + unicode('‘’“”…๑๒๓๔๕๖๗๘๙๐','utf-8'))})
                
                #Lemmatization
                if t in self.lemma_dict:
                    lemma_count += 1
                  
                # remove stop word
                if t not in self.stopwords and t.strip():
                    filteredtext.append(t)
                else:
                    stopword_count += 1
            
            # we will do word segmentation using only a space.            
            filteredtext = ' '.join([l for l in filteredtext])
            text_result.append(filteredtext)
        
        self.test_text_id = text_id
        self.test_text_result = text_result
    
    def get_test_text(self):
        if not self.test_text_id:
            self.load_test_text()
            
        return self.test_text_id, self.test_text_result
        
if __name__ == '__main__':              
    raw = RawData()
    raw.load(0 if len(sys.argv) < 2 else int(sys.argv[1]),False)
    raw.show_tag_summary()
    
    target_tag = 7;
    text,tag,test_text,test_tag = raw.get_train_test_data_tag(target_tag,True)
    print "train = %d, test = %d" % (len(tag),len(test_tag))
    
    sys.exit()
    
    def custom_preprocessor(str):
        str = str.translate({ord(char): None for char in string.punctuation})
        return str
            
    def custom_tokenizer(str):
        return str.split(' ')
            
    count_vect = CountVectorizer(tokenizer=custom_tokenizer,analyzer = 'word',preprocessor=custom_preprocessor)

    #for i in range(0,len(text)):
    #    print "TEXT = " , text[i]
    #    print "TAG = %d => '%s'" % (tag[i] , raw.get_target_names()[tag[i]])
    #    print
    
    print "text count =",len(text),"tag count =",len(tag)
    tag_count = [[raw.get_target_names()[x],x,tag.count(x)] for x in set(tag)]
    sorted_tag_count= sorted(tag_count, key=operator.itemgetter(2), reverse=True)
    
    print "-- TAG --"
    for num_tag in sorted_tag_count:
        print "%d %s(%d)" % (num_tag[2],num_tag[0],num_tag[1])
    
    X_train_counts = count_vect.fit_transform(text)

    #print "### target names ###"
    #for target_name in raw.get_target_names():
    #    print target_name
    
    # word frequence
    words = dict()
    count_dump =  X_train_counts.toarray()
    for i in range(0,count_dump.shape[1]):
        words[count_vect.get_feature_names()[i]] = sum(count_dump[:,i])
        #print "%s %d" % (count_vect.get_feature_names()[i],sum(count_dump[:,i]))
    
    sorted_words = sorted(words.items(), key=operator.itemgetter(1), reverse=True)
    
    print "-- WORD --"
    for word, count in sorted_words[0:5] + sorted_words[-5:-1]:
        print "%5d %s" % (count,word)
