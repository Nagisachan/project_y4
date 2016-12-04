from sklearn.feature_extraction.text import CountVectorizer

vectorizer = CountVectorizer(min_df=1)

corpus = ['This is the first document.',
		'This is the second second document.',
		'And the third one.',
		'Is this the first document?',
		]
		
x = vectorizer.fit_transform(corpus)
print x
print x.toarray()

feature = vectorizer.get_feature_names()
print feature

document_index = vectorizer.vocabulary_.get('document')
print "word 'document' index=",document_index

#try sth new
xx = vectorizer.transform(['Something completely new.'])
print xx.toarray()