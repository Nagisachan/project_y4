import sys
import string
import os.path
import numpy as np
import logging as log
from sys import argv
from sklearn import metrics
from sklearn.pipeline import Pipeline
from raw_data_fetcher import RawData
from sklearn.externals import joblib
from sklearn.linear_model import SGDClassifier
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.datasets import fetch_20newsgroups
#from sklearn.neural_network import MLPClassifier
#from sklearn.naive_bayes import MultinomialNB
#from sklearn.datasets import fetch_20newsgroups

class CLFModel(object):
    def __init__(self):
        self.logger = log.getLogger('CLFModel')
        self.logger.setLevel(log.DEBUG)
        fh = log.FileHandler('CLFModel.log')
        fh.setLevel(log.DEBUG)
        ch = log.StreamHandler()
        ch.setLevel(log.DEBUG)
        formatter = log.Formatter('%(asctime)s - %(name)s - %(levelname)s - %(message)s')
        fh.setFormatter(formatter)
        ch.setFormatter(formatter)
        self.logger.addHandler(fh)
        self.logger.addHandler(ch)
        
        self.model_file_name = "core_model/SGDClassifier0.model"
        self.count_vect_file_name = "core_model/CountVectorizer0.model"
        self.tfidf_file_name = "core_model/TfidfTransformer0.model"
        
        self.load_model = False
    
        self.count_vect = False
        self.tfidf_transformer = False
        self.clf = False
    
    def run_test(self,n_sample=10,load_mode=False,skip_train=False):
        raw = RawData()
        raw.load(n_sample)
        X_train,y_train = raw.get_train_data()
        X_test,y_test = raw.get_test_data()
        
        self.logger.debug("train sample = %d" % len(y_train))
        self.logger.debug("test sample = %d " % len(y_test))
        
        if load_mode:
            self.load_model()
        else:
            self.create_new_model()
            
        if not skip_train:
            self.train(X_train,y_train,load_mode)
            
        self.predict(X_test,True,y_test,raw.get_target_names())
            
    def load_model(self):
        if os.path.isfile(self.model_file_name):
            self.logger.debug("read model from '%s'" % (self.count_vect_file_name))
            self.logger.debug("read count vect from '%s'" % (self.tfidf_file_name))
            self.logger.debug("read tfidf from '%s'" % (self.model_file_name))
            self.count_vect = joblib.load(self.count_vect_file_name) 
            self.tfidf_transformer = joblib.load(self.tfidf_file_name) 
            self.clf = joblib.load(self.model_file_name)
            self.load_model = True        
        else:
            self.logger.error("model file not found!")
    
    def create_new_model(self):
        def custom_preprocessor(str):
            str = str.translate({ord(char): None for char in string.punctuation})
            return str
            
        def custom_tokenizer(str):
            return str.split(' ')
        
        self.logger.debug("ceate new model")
        self.count_vect = CountVectorizer(tokenizer=custom_tokenizer,analyzer = 'word',preprocessor=custom_preprocessor)
        self.clf = SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42)
        self.tfidf_transformer = TfidfTransformer()
        
    def train(self,x_train_data,y_train_data,partial=False):
        X_train_counts = self.count_vect.fit_transform(x_train_data)
        X_train_tfidf = self.tfidf_transformer.fit_transform(X_train_counts)
        
        if partial:
            self.clf.partial_fit(X_train_tfidf, y_train_data)
        else:
            self.clf.fit(X_train_tfidf, y_train_data)
    
    def predict(self,x_test_data,print_result=False,y_test_data=list(),target_name=list()):
        X_train_counts = self.count_vect.transform(x_test_data)
        X_train_tfidf = self.tfidf_transformer.transform(X_train_counts)
        predicted = self.clf.predict(X_train_tfidf)
        
        if print_result:
            score = np.mean(predicted == y_test_data)
            self.logger.info("score = %.2f" % score)
            
        if print_result and len(y_test_data) > 0:
            self.logger.info(metrics.classification_report(y_test_data, predicted, target_names=target_name))
        
        return predicted
    
    def save_model(self):
        print "- save model to file '%s'" % (self.model_file_name)
        print "- save count vect to file '%s'" % (self.count_vect_file_name)
        print "- save tfidf to file '%s'" % (self.tfidf_file_name)
        joblib.dump(self.clf, model_file_name)
        joblib.dump(self.count_vect, count_vect_file_name)
        joblib.dump(self.tfidf_transformer, tfidf_file_name)

if __name__ == '__main__':
    model = CLFModel()
    #model.run_test(n_sample=100)   

    #fetch raw data
    categories = ['alt.atheism', 'soc.religion.christian','comp.graphics', 'sci.med']
    twenty_train = fetch_20newsgroups(subset='train', categories=categories, shuffle=True, random_state=42)
    twenty_test = fetch_20newsgroups(subset='test', categories=categories, shuffle=True, random_state=42)
    
    print "Using 20 news groups as sample data..."
    model.create_new_model()
    model.train(twenty_train.data,twenty_train.target)
    model.predict(twenty_test.data,True,twenty_test.target,twenty_test.target_names)