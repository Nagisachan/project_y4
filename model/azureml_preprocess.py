import os
import sys

from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.externals import joblib

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

X_train, y_train, X_test, y_test = raw.get_train_test_data_tag(7)

# use only content from (paragraph_id,content)
X_train = [data[1] for data in X_train]
X_test = [data[1] for data in X_test]

if len(y_train) < 50:
    print "[Main] not enough (less than 50)"
    sys.exit(-1)
    
X_count = count_vect.transform(X_train + X_test)
X_tfidf = TfidfTransformer().fit_transform(X_count)

data = X_tfidf.toarray()
y = y_train + y_test

print data.shape
with open('tfidf-7.csv','w') as f:
    for i in range(0,len(data)):
        #f.write("%s,%s\n" % (",".join(str(s) for s in data[i]),str(y[i])))
        f.write("%s,%s\n" % ((X_train + X_test)[i].replace(" ","").encode('UTF-8'),str(y[i])))
