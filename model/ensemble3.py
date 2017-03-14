import os
import sys
import numpy as np

from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.externals import joblib
from collections import defaultdict
from sklearn.model_selection import cross_val_score
from sklearn import metrics

#model
from sklearn.linear_model import SGDClassifier
from sklearn.neural_network import MLPClassifier
from sklearn.svm import LinearSVC
from sklearn.linear_model import RidgeClassifier
from sklearn.svm import LinearSVC
from sklearn.linear_model import SGDClassifier
from sklearn.linear_model import Perceptron
from sklearn.linear_model import PassiveAggressiveClassifier
from sklearn.naive_bayes import BernoulliNB, MultinomialNB
from sklearn.neighbors import KNeighborsClassifier
from sklearn.neighbors import NearestCentroid

from sklearn.ensemble import RandomForestClassifier
from sklearn.ensemble import BaggingClassifier
from sklearn.ensemble import AdaBoostClassifier
from sklearn.ensemble import GradientBoostingClassifier

def custom_preprocessor(str):
    # Do not perform any preprocessing here.
	return str
	
def custom_tokenizer(str):
    # Text must be segmented and separated each word by a space.
	return str.split(' ')

def pass_mode(X_tfidf_train,y_train,fitted_model=False):
    n = 0
    table = np.ndarray(shape=(len(y_train),len(models)))
    
    do_train = fitted_model == False
    
    if do_train:
        fitted_model = {}
        _models = models
    else:
        _models = fitted_model
    
    for model in _models:
        clf = _models[model]

        if do_train:
            #bagging = BaggingClassifier(clf,max_samples=0.5, max_features=0.5)
            bagging = BaggingClassifier(clf,max_samples=0.75, max_features=0.75)
            bagging.fit(X_tfidf_train,y_train)
        else:
            bagging = clf
            
        predicted = bagging.predict_proba(X_tfidf_train)
        
        #score = np.mean(predicted == y_test)
        #matrix = metrics.precision_recall_fscore_support(y_test, predicted,average='binary',pos_label=7)
        
        table[:,n] = [v[1] for v in predicted]
        fitted_model[model] = bagging
        
        n += 1
    return table,fitted_model
    	
# custom
from raw_data_fetcher_dummy import RawData

# read model/transformer from file
count_vect_file_name = "core_model/count_vectorizer_dummy.model"

if not os.path.isfile(count_vect_file_name):
    print "Build transformer first! (python build_text_transformer.py)"
    sys.exit()

count_vect = joblib.load(count_vect_file_name)

print "[Main] reading data..."
raw = RawData()
raw.load()
raw.show_tag_summary()

models = {
    'SVM': SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42),
    'NB': MultinomialNB(alpha=.01),
    'ANN' : MLPClassifier(solver='lbfgs', alpha=1e-5,hidden_layer_sizes=(5, 2), random_state=1),
    'KNN' : KNeighborsClassifier(n_neighbors=10),
    'RDFOREST' : RandomForestClassifier(n_estimators=25),
    'NC' : NearestCentroid(),
    #'ADA-SAMME.R': AdaBoostClassifier(n_estimators=100),
}

scores = 0.
precision = 0.
recall = 0.
f1 = 0.

n_round = 1000
    
for i in range(n_round):
    print "# %d" % i,
    
    X_train, y_train, X_test, y_test = raw.get_train_test_data_tag(7)

    # use only content from (paragraph_id,content)
    X_train = [data[1] for data in X_train]
    X_test = [data[1] for data in X_test]
        
    X_count = count_vect.transform(X_train)
    X_tfidf_train = TfidfTransformer().fit_transform(X_count)

    X_count = count_vect.transform(X_test)
    X_tfidf_test = TfidfTransformer().fit_transform(X_count)
        
    table,fitted_model = pass_mode(X_tfidf_train,y_train)
    
    final_clf = SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42)
    #print [np.mean(t) for t in table]
    #print y_train
    
    final_clf.fit(table,y_train)
    table,fitted_model = pass_mode(X_tfidf_test,y_test,fitted_model)
    
    predicted = final_clf.predict(table)
    score = np.mean(predicted == y_test)
    matrix = metrics.precision_recall_fscore_support(y_test, predicted,average='binary',pos_label=7)
         
    scores += score
    precision += matrix[0]
    recall += matrix[1]
    f1 += matrix[2]
    
    n = i+1.0    
    print "AVG %f (%f, %f, %f)" % (scores/n,precision/n,recall/n,f1/n)
    
print 
n += 1
