#!/usr/bin/env python
# -*- coding: utf-8 -*-
from word_segmentation_spicydog import Tws
from sklearn.feature_extraction.text import CountVectorizer
from pprint import pprint
import sys
import string
import codecs
import json
import glob

class N(object):

    def read_text_tag(self):
        listcreatorname = []
        listfilename = []
        send_text = []
        send_tag = []

        index = 0

        listdata = glob.glob("../sample data/train/*.txt")
        for data in listdata:
            mark = listdata[index].index('-')
            creatorname = listdata[index][21:]
            mark = mark + 1
            filename = listdata[index][mark:]
            listcreatorname.append(creatorname)
            listfilename.append(filename)
            index += 1

        #print listfilename
        #print listcreatorname

        index -= 1
        
        for i in range(0,len(listfilename)):
            filename = "../sample data/" + listfilename[i]
            creatorname = '../sample data/train/' + listcreatorname[i]
       
            #filename = "../sample data/story-baanplong.txt"
            #creatorname = '../sample data/train/chalee-story-baanplong.txt'

            f = codecs.open(filename, "r", "utf-8")
            index = 0
            datatext = []
            traintext = []

            for line in f:
                content = line.rstrip()
                if(content == ''):
                    index = index
                else:
                    #print('index:'+str(index)+'--'+content)
                    datatext.append(content)
                    index = index + 1

            #print datatext[0]

            with open(creatorname) as data_file:    
                data_json = json.load(data_file)

            
            mark = 0

            #print "start"
            for index in range(0,len(datatext)):
                if data_json["paragraph"][index]["dropdowntag"] or data_json["paragraph"][index]["freetag"]:
                    #segment = tws.word_segment(train_text)
                    #traintext.append(" ".join([l for l in segment]))
                    if data_json["paragraph"][index]["freetag"]:
                        send_text.append(datatext[index])
                        send_tag.append(','.join(data_json["paragraph"][index]["dropdowntag"]) + "," + ','.join(data_json["paragraph"][index]["freetag"]))
                    else:
                        send_text.append(datatext[index])
                        send_tag.append(','.join(data_json["paragraph"][index]["dropdowntag"]))
                        #print (','.join(data_json["paragraph"][index]["dropdowntag"]))

        return send_text,send_tag

if __name__ == '__main__':
    text = []
    tag = []
    read = N()
    text,tag = read.read_text_tag()
    for i in range(0,len(text)):
        print i
