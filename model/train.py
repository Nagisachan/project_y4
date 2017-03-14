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

from sklearn.ensemble import RandomForestClassifier
from sklearn.ensemble import BaggingClassifier
from sklearn.ensemble import AdaBoostClassifier
from sklearn.ensemble import GradientBoostingClassifier
from sklearn.ensemble import VotingClassifier

def train(X,y,count_vect,clf,partial=False):
    X_count = count_vect.transform(X)
    X_tfidf = TfidfTransformer().fit_transform(X_count)
    
    if partial:
        clf.partial_fit(X_tfidf, y)
    else:
        clf.fit(X_tfidf, y)

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
           
if len(argv) < 2 or argv[1] not in ('new','old'):
    print "Usage: train <which model (new/old)>"
    sys.exit()
    
# read model/transformer from file
model_file_prefix = "core_model/MODEL"
count_vect_file_name = "core_model/count_vectorizer.model"

if not os.path.isfile(count_vect_file_name):
    print "Build transformer first! (python build_text_transformer.py)"
    sys.exit()

count_vect = joblib.load(count_vect_file_name) 

print "[Main] reading data..."
raw = RawData()
raw.load()

model_major = [
    ('SVM',  BaggingClassifier(SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42),max_samples=0.5, max_features=0.5)),
    ('NB',  BaggingClassifier(MultinomialNB(alpha=.01),max_samples=0.5, max_features=0.5)),
    ('ANN' ,  BaggingClassifier(MLPClassifier(solver='lbfgs', alpha=1e-5,hidden_layer_sizes=(5, 2), random_state=1),max_samples=0.5, max_features=0.5)),
    ('KNN' ,  BaggingClassifier(KNeighborsClassifier(n_neighbors=10),max_samples=0.5, max_features=0.5)),
    ('RDFOREST' , RandomForestClassifier(n_estimators=25)),
    ('NC' ,  BaggingClassifier(NearestCentroid(),max_samples=0.5, max_features=0.5)),
    ('ADA-SAMME.R', AdaBoostClassifier(n_estimators=100)),
]

models = {
    'SVM': SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42),
    'NB': MultinomialNB(alpha=.01),
    'ANN' : MLPClassifier(solver='lbfgs', alpha=1e-5,hidden_layer_sizes=(5, 2), random_state=1),
    'KNN' : KNeighborsClassifier(n_neighbors=10),
    'RDFOREST' : RandomForestClassifier(n_estimators=25),
    'NC' : NearestCentroid(),
    'MAJOR' : VotingClassifier(estimators=models,weights=weights,voting='soft',n_jobs=-1)
}

mode_name = "SVM"
if len(argv) >= 3:
    if argv[2] in models.iterkeys():
        mode_name = argv[2]
    else:
        print "[Main] invalid model name '%s', please use (%s)", (argv[2],",".join(models.iterkeys()))
        print "[Main] use default mode '%s'" % mode_name
else:
    print "[Main] use default mode '%s'" % mode_name
    
# get all tag index
all_tag_idx = raw.get_all_tag_idx()
model_tags = dict()
        
model_from_file = argv[1] == "old"
if model_from_file and os.path.isfile(model_file_prefix):
    print "[Main] read model from prefix '%s'" % (model_file_prefix)
    
    for tag in all_tag_idx:
        clf = joblib.load(model_file_prefix + '-' + mode_name + '-' + str(tag) + '.model')
        model_tags[tag] = clf
else:
    print "[Main] ceate new models"
    for tag in all_tag_idx:
        print "[Main] ceate model for %d" % tag
        clf = SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42)
        model_tags[tag] = clf

train_tag_list = []
avg_score = []
for target_tag in all_tag_idx:
    X_train, y_train, X_test, y_test = raw.get_train_test_data_tag(target_tag)
    
    # use only content from (paragraph_id,content)
    X_train = [data[1] for data in X_train]
    X_test = [data[1] for data in X_test]
    
    if len(y_train) < 200:
        #print "[Main] not enough (%3d less than 200) sample for '%s'" % (len(y_train),raw.get_target_names()[target_tag].encode('utf-8'))
        continue
    
    train_tag_list.append(target_tag)
    train(X_train,y_train,count_vect,model_tags[target_tag],model_from_file)
        
    predicted = predict(X_test,count_vect,model_tags[target_tag])
    score = np.mean(predicted == y_test)
    avg_score.append(score)
    
    matrix = metrics.precision_recall_fscore_support(y_test, predicted,average='binary',pos_label=target_tag)
    precision = matrix[0]
    recall = matrix[1]
    f1 = matrix[2]
    
    print "%s score %.2f (%s)-[%d/%d]" % (mode_name,score,raw.get_target_names()[target_tag].encode('utf-8'),len(y_train),len(y_test))
    print "precision=%.2f, recall=%.2f, f1=%.2f" % (precision,recall,f1)
    print
    
print "Average score of %s = %.2f" % (mode_name,np.mean(avg_score))

print "Save model? (yes/No)"
save = raw_input();

if save.upper()[0] == "Y" or save.upper() == "YES":
    for tag in train_tag_list:
        output_filename = model_file_prefix + '-' + mode_name + '-' + str(tag) + '.model'
        print "[Main] save model '%s'" % (output_filename)
        joblib.dump(model_tags[tag], output_filename)
