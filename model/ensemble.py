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
    'ADA-SAMME.R': AdaBoostClassifier(n_estimators=100),
}

scores = defaultdict(int)
precision = defaultdict(int)
recall = defaultdict(int)
f1 = defaultdict(int)

n_round = 1000
n=1

for i in range(n_round):
    print "# %d" % n
    for model in models:
        clf = models[model]
        
        X_train, y_train, X_test, y_test = raw.get_train_test_data_tag(7)

        # use only content from (paragraph_id,content)
        X_train = [data[1] for data in X_train]
        X_test = [data[1] for data in X_test]

        if len(y_train) < 50:
            print "[Main] not enough (less than 50)"
            continue
            
        X_count = count_vect.transform(X_train)
        X_tfidf_train = TfidfTransformer().fit_transform(X_count)

        X_count = count_vect.transform(X_test)
        X_tfidf_test = TfidfTransformer().fit_transform(X_count)

        bagging = BaggingClassifier(clf,max_samples=0.5, max_features=0.5)
        #bagging = BaggingClassifier(clf,max_samples=0.75, max_features=0.75)
        bagging.fit(X_tfidf_train,y_train)
        predicted = bagging.predict(X_tfidf_test)
        score = np.mean(predicted == y_test)
        matrix = metrics.precision_recall_fscore_support(y_test, predicted,average='binary',pos_label=7)
        
        scores[model] += score
        precision[model] += matrix[0]
        recall[model] += matrix[1]
        f1[model] += matrix[2]
        
        print "AVG %s -> %f (%f, %f, %f)" % (model,scores[model]/n,precision[model]/n,recall[model]/n,f1[model]/n)
    print
    n += 1
