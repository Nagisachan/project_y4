from impala.dbapi import connect
import socket

class HiveDB(object):
    def __init__(self):
        self.tag_dict = False
        self.local_ws = ("localhost", 9999)
    
    def receive(self,sock):
        chunks = []
        bytes_recd = 0
        while True:
            chunk = sock.recv(4096)
            if chunk == '':
                break
            chunks.append(chunk)
        return ''.join(chunks)
            
    def get_all_tag(self,print_result=False): 
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect(self.local_ws)
        s.send("getalltags\r\n")
        tags_table = self.receive(s)
        tags_table = tags_table.split("\n")
        
        result = []
        for line in tags_table:
            if line:
                result.append(tuple(line.split(",")))
        
        tags_table = result
        
        if print_result:
            for tag in tags_table:
                print "%3s %s" % tag
         
        return tags_table
        
    def get_all_text_tag(self,print_result=False):            
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect(self.local_ws)
        s.send("getalltexttags\r\n")
        texttags_table = self.receive(s)
        texttags_table = texttags_table.split("\n")
        
        result = []
        for line in texttags_table:
            if line:
                result.append(tuple(line.split(",")))
        
        texttags_table = result
        
        if print_result:
            for tag in texttags_table:
                print "%5s %s %s" % tag
         
        return texttags_table
    
    def read_text_tag(self,print_result=False):            
        texts_tags = self.get_all_text_tag()
        
        from collections import defaultdict
        text_tag_list_uniq = defaultdict(list)
        
        for row in texts_tags:
            text_tag_list_uniq[row[0]].append(str(row[1]))
        
        text_tag_str_uniq = dict()     
        for key in text_tag_list_uniq:
            text_tag_str_uniq[key] = ",".join(list(set(text_tag_list_uniq[key])))
                
        result_text_id = []
        result_text_content = []
        result_tag = []
        
        for paragraph_id in text_tag_str_uniq:
            for row in texts_tags:
                if row[0] == paragraph_id:
                    content = row[2]
                    break
            
            result_text_id.append(paragraph_id)
            result_text_content.append(content)
            result_tag.append(text_tag_str_uniq[paragraph_id])
        
        return result_text_id,result_text_content,result_tag
     
    def get_tag_name(self,tag_id):
        if self.tag_dict == False:
            self.tag_dict = dict()
        
            for tag in self.get_all_tag():
                self.tag_dict[tag[0]] = tag[1]
            
        return self.tag_dict[tag_id]
    
    def read_test_text(self,print_result=False):
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.connect(self.local_ws)
        s.send("readtesttext\r\n")
        texts_table = self.receive(s)
        texts_table = texts_table.split("\n")
        
        result = []
        for line in texts_table:
            if line:
                result.append(tuple(line.split(",")))
        
        texts_table = result
        
        if print_result:
            for texts in texts_table:
                print "%5s %s" % (texts[0],texts[1][:20])
                
        result_text_id = []
        result_text_content = []
        
        for texts in texts_table:
            result_text_id.append(texts[0])
            result_text_content.append(texts[1])
        
        return result_text_id,result_text_content
     
         
    def write_result(self,paragraph_id,tag_list):
        print "%s >> %s" % (paragraph_id,",".join([str(t) for t in tag_list]))            
        
        for tag in tag_list:
            paragraph_ids = paragraph_id.split('-')
            s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            s.connect(self.local_ws)
            s.send("writeresult:%s:%d:%d\r\n" % (paragraph_id,tag['tag'],tag['score']))
        
if __name__ == '__main__':
    #HiveDB().get_all_tag(True)
    #HiveDB().get_all_text_tag(True)
    #HiveDB().read_text_tag(True)
    #HiveDB().read_test_text(True)
    
    """
    from collections import defaultdict
    frequency = defaultdict(int)
    
    impala = ImpalaDB()
    for data in impala.read_text_tag(False)[2]:
        data = data.split(',')
        for tag in data:
            if tag.strip():
                frequency[int(tag)] += 1
    
    import operator
    sorted_frequency = sorted(frequency.items(), key=operator.itemgetter(1), reverse=True)
    for row in sorted_frequency:
        print "ID:%-2d   %4d %s" % (row[0],row[1],impala.get_tag_name(row[0]))
    """
    
    HiveDB().write_result("2-15",[{'tag':1,'score':95},{'tag':2,'score':81},{'tag':3,'score':75},{'tag':4,'score':50}]);
