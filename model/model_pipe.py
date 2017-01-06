import numpy as np
from sklearn.pipeline import Pipeline
from sklearn.datasets import fetch_20newsgroups
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.naive_bayes import MultinomialNB
from sklearn.linear_model import SGDClassifier
from sklearn.neural_network import MLPClassifier
from sklearn import metrics

#fetch raw data
categories = ['alt.atheism', 'soc.religion.christian','comp.graphics', 'sci.med']
twenty_train = fetch_20newsgroups(subset='train', categories=categories, shuffle=True, random_state=42)

# create pipeline
text_clf = Pipeline([('vect', CountVectorizer()), ('tfidf', TfidfTransformer()), ('clf', MultinomialNB()),])

# train model in signle line
text_clf = text_clf.fit(twenty_train.data, twenty_train.target)

# fetch test data
twenty_test = fetch_20newsgroups(subset='test', categories=categories, shuffle=True, random_state=42)
docs_test = twenty_test.data
predicted = text_clf.predict(docs_test)

# score
score = np.mean(predicted == twenty_test.target)
print "MultinomialNB",score

# using different model (SVM)
text_clf = Pipeline([('vect', CountVectorizer()),('tfidf', TfidfTransformer()),('clf', SGDClassifier(loss='hinge', penalty='l2',alpha=1e-3, n_iter=5, random_state=42)),])
text_clf=text_clf.fit(twenty_train.data, twenty_train.target)
predicted = text_clf.predict(docs_test)
score = np.mean(predicted == twenty_test.target)
print "SVM",score


# using different model (multi-layer perceptron)
text_clf = Pipeline([('vect', CountVectorizer()),('tfidf', TfidfTransformer()),('clf', MLPClassifier(solver='lbfgs', alpha=1e-5, hidden_layer_sizes=(5, 2), random_state=1)),])
text_clf=text_clf.fit(twenty_train.data, twenty_train.target)
predicted = text_clf.predict(docs_test)
score = np.mean(predicted == twenty_test.target)
print "NN",score

# view more info
print(metrics.classification_report(twenty_test.target, predicted, target_names=twenty_test.target_names))
print metrics.confusion_matrix(twenty_test.target, predicted)