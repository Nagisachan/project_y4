# Tokenizing text	
from sklearn.feature_extraction.text import CountVectorizer	
count_vect = CountVectorizer()
text = ["I love python and love programming.","I love programming but not python."]

X_train_counts = count_vect.fit_transform(text)
print text
print type(X_train_counts)
print X_train_counts

index = 0
for word in count_vect.get_feature_names():
	print index, word
	index += 1