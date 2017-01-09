from word_segmentation_spicydog import Tws
from sklearn.feature_extraction.text import CountVectorizer
from read_training_data import N
import sys
import string

text = []
tag = []

tmp_text = []
tmp_tag = []

read = N()
tws = Tws()

tmp_text,tmp_tag = read.read_text_tag()

for i in range(0,len(tmp_text)):
        print i

with open("../sample data/chatralada-story-baan-kho-testingdata-tmp.txt","r") as f:
	for line in f:
		if len(text) == len(tag):
			line = line.strip()
			line = tws.word_segment(line)
			
			text.append(" ".join([l for l in line]))
		else:
			tag.append(line.strip().split(',')[0])

for i in range(0,len(text)):
	print "TEXT = " , text[i]
	print "TAG = " , tag[i]	

def custom_preprocessor(str):
	str = str.translate({ord(char): None for char in string.punctuation})
	return str
	
def custom_tokenizer(str):
	return str.split(' ')
	
count_vect = CountVectorizer(tokenizer=custom_tokenizer,analyzer = 'word',preprocessor=custom_preprocessor)

X_train_counts = count_vect.fit_transform(text)
print(type(X_train_counts))
print(X_train_counts)

index = 0
for word in count_vect.get_feature_names():
	print index, word
	index += 1
