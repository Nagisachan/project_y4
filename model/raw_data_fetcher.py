# -*- encoding: utf-8 -*-

#from word_segmentation_spicydog import Tws
from word_segmentation_insightera import Tws
from sklearn.feature_extraction.text import CountVectorizer
from read_training_data import N
from sklearn.model_selection import train_test_split
import numpy as np
import sys
import string
import codecs
import operator
import random

class RawData(object):
    def __init__(self):
        self.read = N()
        self.tws = Tws()
        self.train_text_ratio = 0.8
        self.tag_table = {'OTHER':0}
        self.tag_inverse_table = {0:'OTHER'}
        self.raw_paragraph_text = []
        self.raw_paragraph_tag = []
        
    def load(self,sample_n):
        text = []
        tag = []

        stopwords = codecs.open('stop_words.txt', 'r','utf-8').read().split()
        space = ' '
        
        #for stopword in stopwords:
        #    print stopword

        # read raw data
        text,tag = self.read.read_text_tag()

        if sample_n > 0:
            text = text[:sample_n]
            tag = tag[:sample_n]
            
        print "[RawData]: fetch from %d paragraphs" % len(tag)
        
        # create 1 tag per doc raw data
        new_text = []
        new_tag = []
        for i in range(0,len(text)):
            filteredtext = []
            tmp_text = self.tws.word_segment(text[i].strip())
            
            # preprocess
            for t in tmp_text:
                # remove punctuation
                t = t.translate({ord(char): None for char in (string.punctuation + unicode('‘’“”…๑๒๓๔๕๖๗๘๙๐','utf-8'))})
                
                # remove stop word
                if t not in stopwords and t.strip():
                    filteredtext.append(t)
                    
            # we will do word segmentation using only a space.            
            filteredtext = space.join([l for l in filteredtext])
            
            tmp_tag = tag[i].split(',')
            tmp_tag = [t.strip() for t in tmp_tag]
            
            all_tag = []
            for t in tmp_tag:
                if t.strip() == '':
                    continue;
            
                # convert tag to int
                if t not in self.tag_table:
                    idx = len(self.tag_table)
                    self.tag_table[t] = idx
                    self.tag_inverse_table[idx] = t
                else:
                    idx = self.tag_table[t];
                    
                new_text.append(filteredtext)
                new_tag.append(idx)
                all_tag.append(idx)
            
            self.raw_paragraph_text.append(filteredtext)
            self.raw_paragraph_tag.append(all_tag)

        print
        
        X = new_text
        y = new_tag
        
        # train/test split using library
        self.X_train, self.X_test, self.y_train, self.y_test = train_test_split(X, y, test_size=0.33, random_state=42)

        # random sample manually
        text = []
        tag = []
        m = len(new_tag)
        rand_index = random.sample(range(m),m)
        for idx in rand_index:
            text.append(new_text[idx])
            tag.append(new_tag[idx])
                        
        self.text = text
        self.tag = tag
        
        # random for raw
        text = []
        tag = []
        m = len(self.raw_paragraph_tag)
        rand_index = random.sample(range(m),m)
        for idx in rand_index:
            text.append(self.raw_paragraph_text[idx])
            tag.append(self.raw_paragraph_tag[idx])
                        
        self.raw_paragraph_text = text
        self.raw_paragraph_tag = tag
        
    def get_train_data(self):
        train_data_count = int(len(self.tag)*self.train_text_ratio)
        
        # manual random
        return self.text[:train_data_count],self.tag[:train_data_count] 
        
        # library random
        #return self.X_train,self.y_train 
        
    def get_test_data(self):
        train_data_count = int(len(self.tag)*self.train_text_ratio)
        
        # manual random
        return self.text[train_data_count:],self.tag[train_data_count:] 
        
        # library random
        #return self.X_test,self.y_test
    
    def get_train_test_data_tag(self,tag_idx):
        #print "Get tag %s(%d) = %d" % (self.tag_inverse_table[tag_idx],tag_idx,[tag_idx in b for b in self.raw_paragraph_tag].count(True))
        match_text = []
        not_match_text = []
        
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
        
        train_data_count = int(len(match_text)*self.train_text_ratio)
        
        test_match_text = match_text[train_data_count:]
        test_not_match_text = not_match_text[train_data_count:]
        
        train_match_text = match_text[:train_data_count]
        train_not_match_text = not_match_text[:train_data_count]
        
        # for test
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
        
        # for train
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
            words = text.split(' ')
            for token in words:
                frequency[token] += 1
        
        all_word_count = len(frequency.keys())
        all_word_occur_count = sum(frequency.values())
        min_threshold = int(0.005*all_word_count)
        max_threshold = int(0.6*all_word_count)
        all_filtered_word_count = len([word for word in frequency.keys() if frequency[word] > min_threshold and frequency[word] < max_threshold])
        
        sorted_frequency = sorted(frequency.items(), key=operator.itemgetter(1), reverse=True)
        
        #for fword,fvalue in sorted_frequency[:10] + sorted_frequency[-5:]:
        #    print fword,fvalue
        
        all_filtered_word_occur_count = 0
        for i in range(len(train_text)):
            words = train_text[i].split(' ')
            tmp = [word for word in words if frequency[word] < max_threshold and frequency[word] > min_threshold]
            all_filtered_word_occur_count += len(tmp)
            train_text[i] = " ".join(tmp) 

        #print "min=%d max=%d before=%d/%d after=%d/%d" % (min_threshold,max_threshold,all_word_occur_count,all_word_count,all_filtered_word_occur_count,all_filtered_word_count)
        
        return train_text,train_tag,test_text,test_tag
        
    def get_target_names(self):
        tmp = []
        for i in range(0,len(self.tag_inverse_table)):
            tmp.append(self.tag_inverse_table[i])
        return tmp
        
    def show_tag_summary(self):
        summary = []
        for name, idx in self.tag_table.iteritems():
            summary.append(("%s(%d)" % (name,idx),[idx in t for t in self.raw_paragraph_tag].count(True)))
            
        sorted_summary= sorted(summary, key=operator.itemgetter(1), reverse=True)
        for tag, count in sorted_summary[:10]:
            print "%5d %s" % (count,tag)
    
    def get_all_tag_idx(self):
        return self.tag_inverse_table.iterkeys()
        
if __name__ == '__main__':              
    raw = RawData()
    raw.load(0 if len(sys.argv) < 2 else int(sys.argv[1]))
    raw.show_tag_summary()
    
    target_tag = 6;
    #text,tag = raw.get_train_data()
    text,tag,test_text,test_tag = raw.get_train_test_data_tag(target_tag)
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