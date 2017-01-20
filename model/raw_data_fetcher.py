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

class RawData(object):
        def __init__(self):
                self.read = N()
                self.tws = Tws()
                self.train_text_ratio = 0.9
                self.tag_table = {}
                self.tag_inverse_table = {}
                
        def load(self,sample_n):
                text = []
                tag = []

                stopwords = codecs.open('stop_words.txt', 'r','utf-8').read().split()
                space = ' '
                #print stopwords

                # read raw data
                text,tag = self.read.read_text_tag()

                print text[0]

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
                        for t in tmp_text:
                                if t not in stopwords:
                                        if t != space:
                                                filteredtext.append(t)
                                
                        tmp_text = " ".join([l for l in tmp_text])
                        filteredtext = " ".join([l for l in filteredtext])

                        #print tmp_text
                        #print filteredtext
                        
                        tmp_tag = tag[i].split(',')
                        tmp_tag = [t.strip() for t in tmp_tag]
                        
                        for t in tmp_tag:
                        
                                # convert tag to int
                                if t not in self.tag_table:
                                        idx = len(self.tag_table)
                                        self.tag_table[t] = idx
                                        self.tag_inverse_table[idx] = t
                        
                                new_text.append(filteredtext)
                                new_tag.append(t)
                
                X = new_text
                y = new_tag
                
                self.X_train, self.X_test, self.y_train, self.y_test = train_test_split(X, y, test_size=0.33, random_state=42)

                # random sample
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
                return self.text[:train_data_count],self.tag[:train_data_count]
                #return self.X_train,self.y_train
                
        def get_test_data(self):
                train_data_count = int(len(self.tag)*self.train_text_ratio)
                return self.text[train_data_count:],self.tag[train_data_count:]
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

        for i in range(0,len(text)):
                print "TEXT = " , text[i]
                print "TAG = " , tag[i] 
        print "text count =",len(text),"tag count =",len(tag)
        
        X_train_counts = count_vect.fit_transform(text)
        #print(type(X_train_counts))
        #print(X_train_counts)

        index = 0
        for word in count_vect.get_feature_names():
                #print index, word
                index += 1

        #print raw.tag_table
        #print raw.tag_inverse_table
        print raw.get_target_names()
