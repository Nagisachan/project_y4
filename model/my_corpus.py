from gensim import corpora
from collections import defaultdict

class MyCorpus(object):
	def __init__(self):
		self.dictionary = corpora.Dictionary.load('deerwester.dict')
	def __iter__(self):
		for line in open('mycorpus.txt'):
			# assume there's one document per line, tokens separated by whitespace
			yield self.dictionary.doc2bow(line.lower().split())
			
corpus_memory_friendly = MyCorpus()  # doesn't load the corpus into memory!
for vector in corpus_memory_friendly:  # load one vector into memory at a time
	print(vector)