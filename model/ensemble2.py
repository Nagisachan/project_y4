import os
import sys

from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.externals import joblib
from collections import defaultdict
from sklearn.model_selection import cross_val_score

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
from sklearn.ensemble import VotingClassifier

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

models = [
    ('SVM',  BaggingClassifier(SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42),max_samples=0.5, max_features=0.5)),
    ('NB',  BaggingClassifier(MultinomialNB(alpha=.01),max_samples=0.5, max_features=0.5)),
    ('ANN' ,  BaggingClassifier(MLPClassifier(solver='lbfgs', alpha=1e-5,hidden_layer_sizes=(5, 2), random_state=1),max_samples=0.5, max_features=0.5)),
    ('KNN' ,  BaggingClassifier(KNeighborsClassifier(n_neighbors=10),max_samples=0.5, max_features=0.5)),
    ('RDFOREST' , RandomForestClassifier(n_estimators=25)),
    ('NC' ,  BaggingClassifier(NearestCentroid(),max_samples=0.5, max_features=0.5)),
    ('ADA-SAMME.R', AdaBoostClassifier(n_estimators=100)),
]

clf = VotingClassifier(estimators=models,weights=[0.2,0.14,0.14,0.14,0.14,0.1,0.14],voting='hard',n_jobs=-1)

scores = defaultdict(int)
n_round = 100
for i in range(n_round):
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

    clf.fit(X_tfidf_train,y_train)
    
    score = clf.score(X_tfidf_test,y_test)
    print "%s -> %f" % ("MAJOR",score)
    
    scores["MAJOR"] += score
        
    print "-------"
        
for model in scores:
    print "AVG %s -> %f" % (model,scores[model]/n_round)
