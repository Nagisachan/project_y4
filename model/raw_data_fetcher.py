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

class RawData(object):
    def __init__(self):
        self.read = N()
        self.tws = Tws()
        self.train_text_ratio = 0.8
        self.tag_table = {}
        self.tag_inverse_table = {}
        
    def load(self,sample_n):
        text = []
        tag = []

        stopwords = codecs.open('stop_words.txt', 'r','utf-8').read().split()
        space = ' '
        
        #for stopword in stopwords:
        #    print stopword

        # read raw data
        text,tag = self.read.read_text_tag()

        #print text[0]

        if sample_n > 0:
            text = text[:sample_n]
            tag = tag[:sample_n]
            
        print "[RawData]: fetch from %d docs" % len(tag)
        
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
            #tmp_text = space.join([l for l in tmp_text]) 
            filteredtext = space.join([l for l in filteredtext])
            
            tmp_tag = tag[i].split(',')
            tmp_tag = [t.strip() for t in tmp_tag]
            
            for t in tmp_tag:
                if t.strip() == '':
                    continue;
            
                # convert tag to int
                if t not in self.tag_table:
                    idx = len(self.tag_table)
                    self.tag_table[t] = idx
                    self.tag_inverse_table[idx] = t
        
                new_text.append(filteredtext)
                new_tag.append(t)
    
        X = new_text
        y = new_tag
        
        # train/test split using library
        self.X_train, self.X_test, self.y_train, self.y_test = train_test_split(X, y, test_size=0.33, random_state=42)

        # random sample manually
        text = []
        tag = []
        m = len(new_tag)
        rand_index = np.random.choice(range(1,m),m)
        for idx in rand_index:
                text.append(new_text[idx])
                tag.append(self.tag_table[new_tag[idx]])
                        
        self.text = text
        self.tag = tag
        
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

    def get_target_names(self):
        tmp = []
        for i in range(0,len(self.tag_inverse_table)):
            tmp.append(self.tag_inverse_table[i])
        return tmp
        
if __name__ == '__main__':              
    raw = RawData()
    raw.load(0)
    text,tag = raw.get_train_data()
    
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
    
    print
    print "text count =",len(text),"tag count =",len(tag)
    
    X_train_counts = count_vect.fit_transform(text)

    #index = 0
    #for word in count_vect.get_feature_names():
    #    print index, word
    #    index += 1

    print "### target names ###"
    for target_name in raw.get_target_names():
        print target_name
    
    words = dict()
    count_dump =  X_train_counts.toarray()
    for i in range(0,count_dump.shape[1]):
        words[count_vect.get_feature_names()[i]] = sum(count_dump[:,i])
        #print "%s %d" % (count_vect.get_feature_names()[i],sum(count_dump[:,i]))
    
    sorted_words = sorted(words.items(), key=operator.itemgetter(1), reverse=True)    
    
    for word, count in sorted_words[0:100] + sorted_words[-100:-1]:
        print "%5d %s" % (count,word)