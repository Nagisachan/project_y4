# -*- encoding: utf-8 -*-

import numpy as np
from sklearn.pipeline import Pipeline
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.naive_bayes import MultinomialNB
from sklearn.linear_model import SGDClassifier
from sklearn.neural_network import MLPClassifier
from sklearn import metrics
from raw_data_fetcher import RawData
import string
from sklearn.externals import joblib
import os.path
from sys import argv
from collections import defaultdict
import sys
from sklearn.linear_model import RidgeClassifier
from sklearn.svm import LinearSVC
from sklearn.linear_model import SGDClassifier
from sklearn.linear_model import Perceptron
from sklearn.linear_model import PassiveAggressiveClassifier
from sklearn.naive_bayes import BernoulliNB, MultinomialNB
from sklearn.neighbors import KNeighborsClassifier
from sklearn.neighbors import NearestCentroid
from sklearn.ensemble import RandomForestClassifier
from impala_db import ImpalaDB

def predict(X,count_vect,clf):
    X_count = count_vect.transform(X)
    X_tfidf = TfidfTransformer().fit_transform(X_count)
    return clf.predict(X_tfidf)

def custom_preprocessor(str):
    # Do not perform any preprocessing here.
	return str
	
def custom_tokenizer(str):
    # Text must be segmented and separated each word by a space.
	return str.split(' ')
           
if len(argv) < 2:
    print "Usage: test <model name>"
    sys.exit()

mode_name = argv[1]
    
# read model/transformer from file
model_file_prefix = "core_model/MODEL"
count_vect_file_name = "core_model/count_vectorizer.model"

if not os.path.isfile(count_vect_file_name):
    print "Build transformer first! (python build_text_transformer.py)"
    sys.exit()

count_vect = joblib.load(count_vect_file_name) 

print "[Main] reading data..."
raw = RawData()
   
# get all tag index
all_tag_idx = raw.get_all_tag_idx()
model_tags = dict()
        
print "[Main] read model from prefix '%s'" % (model_file_prefix)
for tag in all_tag_idx:
    filename = model_file_name = model_file_prefix + '-' + mode_name + '-' + str(tag) + '.model'
    if os.path.isfile(filename):
        clf = joblib.load(filename)
        model_tags[tag] = clf
        print "[Main] load model %s" % filename

db = ImpalaDB()
avg_score = []
for target_tag in all_tag_idx:
    if target_tag not in model_tags.iterkeys():
        continue
            
    text_id,text = raw.get_test_text()
        
    predicted = predict(text,count_vect,model_tags[target_tag])
    
    for paragraph_id,target_class in zip(text_id,predicted):
        if target_class != 0:
            print "%s is %s" % (paragraph_id,raw.get_target_names()[target_class])
            db.write_result(paragraph_id,[target_class]);
                
    
