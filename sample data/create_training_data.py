import codecs
import json
from pprint import pprint

filename = "story-satun-2.txt"
creatorname = 'wimonsri-story-satun-2.txt'
output = "wimonsri-story-satun-2-trainingdata.txt"

f = codecs.open(filename, "r", "utf-8")
index = 0
datatext = []

for line in f:
    content = line.rstrip()
    if(content == ''):
        print "[found an end of line]"
    else:
        #print('index:'+str(index)+'--'+content)
        datatext.append(content)
        index = index + 1 
        
print "--------------"
index = 0

for x in datatext:
    #print ('index:'+str(index)+'--'+x)
    index = index + 1 

with open(creatorname) as data_file:    
    data_json = json.load(data_file)


    
print(data_json["paragraph"][0])


################

out = ""

for i in range(0,index):
    if data_json["paragraph"][i]["dropdowntag"] or data_json["paragraph"][i]["freetag"]:
        #print (str(i)+": "+'data=["'+datatext[i]+'"],tag=["'+'","'.join(data_json["paragraph"][i]["dropdowntag"])+'"]\n')
        if data_json["paragraph"][i]["freetag"]:
            out = out +('data=["'+datatext[i]+'"],tag=["'+'","'.join(data_json["paragraph"][i]["dropdowntag"])+'","'+'","'.join(data_json["paragraph"][i]["freetag"])+'"]\n')
        else:
            out = out +('data=["'+datatext[i]+'"],tag=["'+'","'.join(data_json["paragraph"][i]["dropdowntag"])+'"]\n')

file = codecs.open(output, "w", "utf-8")
file.write(out)
file.close()
