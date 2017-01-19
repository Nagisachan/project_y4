import numpy as np
from sklearn.pipeline import Pipeline
#from sklearn.datasets import fetch_20newsgroups
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer
#from sklearn.naive_bayes import MultinomialNB
from sklearn.linear_model import SGDClassifier
#from sklearn.neural_network import MLPClassifier
from sklearn import metrics
from raw_data_fetcher import RawData
import string
from sklearn.externals import joblib
import os.path
from sys import argv

"""
# fetch raw data
categories = ['alt.atheism', 'soc.religion.christian','comp.graphics', 'sci.med']

# fetch train data
twenty_train = fetch_20newsgroups(subset='train', categories=categories, shuffle=True, random_state=42)
#print "===== Text =====\n",twenty_train.data[0]
#print "===== Class =====\n",twenty_train.target[0]

# fetch test data
twenty_test = fetch_20newsgroups(subset='test', categories=categories, shuffle=True, random_state=42)
#print "===== Text =====\n",twenty_test.data[0]
#print "===== Class =====\n",twenty_test.target[0]
"""

raw = RawData()
raw.load(15)

text,tag = raw.get_train_data()
twenty_train_data = text
twenty_train_target = tag
print "===== train ====="
#for text,tag in zip(twenty_train_data,twenty_train_target):
#	print text,tag

text,tag = raw.get_test_data()
twenty_test_data = text
twenty_test_target = tag
print "===== test ====="
#for text,tag in zip(twenty_test_data,twenty_test_target):
#	print text,tag
	
# prepare Custom Count Vectorizer
def custom_preprocessor(str):
	str = str.translate({ord(char): None for char in string.punctuation})
	return str
	
def custom_tokenizer(str):
	return str.split(' ')
	
count_vect = CountVectorizer(tokenizer=custom_tokenizer,analyzer = 'word',preprocessor=custom_preprocessor)

# show info
print "train sample =",len(twenty_train_target)
print "test sample =",len(twenty_test_target)

# read model from file
model_file_name = "core_model/SGDClassifier0.model"
if os.path.isfile(model_file_name) and len(argv) > 1 and argv[1] != "0":
    print "read model from '%s'" % (model_file_name)
    clf = joblib.load(model_file_name) 
else:
    print "ceate new model"
    clf = SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42)

# SVM
text_clf = Pipeline([('vect', count_vect),('tfidf', TfidfTransformer()),('clf', clf)])

# save model to file
if len(argv) > 3 and argv[3] != "0":
    print "skip train model..."
    text_clf = clf
else:
    print "train model..."
    text_clf=text_clf.fit(twenty_train_data, twenty_train_target)

# save model to file
if len(argv) > 2 and argv[2] != "0":
    print "save model to file '%s'" % (model_file_name)
    joblib.dump(clf, model_file_name)
    
predicted = text_clf.predict(twenty_test_data)
score = np.mean(predicted == twenty_test_target)
print "SVM",score

# view more info
print(metrics.classification_report(twenty_test_target, predicted, target_names=raw.get_target_names()))
#print metrics.confusion_matrix(twenty_test_target, predicted)
