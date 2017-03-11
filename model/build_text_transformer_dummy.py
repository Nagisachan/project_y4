from sklearn.feature_extraction.text import CountVectorizer
from raw_data_fetcher_dummy import RawData
from sklearn.externals import joblib

def custom_preprocessor(str):
    # Do not perform any preprocessing here.
	return str
	
def custom_tokenizer(str):
    # Text must be segmented and separated each word by a space.
	return str.split(' ')

# intialize transformers    
count_vect = CountVectorizer(analyzer = 'word',tokenizer=custom_tokenizer,preprocessor=custom_preprocessor)

# load all text
raw = RawData()
raw.load(0)

# fit transformers
texts = raw.get_all_text()
count_vect.fit_transform([ text[1] for text in texts])

# export transformer
joblib.dump(count_vect, "core_model/count_vectorizer_dummy.model")

