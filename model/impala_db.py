from impala.dbapi import connect

class ImpalaDB(object):
    def __init__(self):
            conn = connect(host='23.97.59.54', port=21050, database='thaiautotag')
            self.cursor = conn.cursor()
        
    def get_all_tag(self,print_result=False):            
        self.cursor.execute('SELECT tagid, tagcontent FROM tag_data ORDER BY tagid')
        tags_table = self.cursor.fetchall()
        
        if print_result:
            for tag in tags_table:
                print "%3s %s" % tag
         
        return tags_table
        
    def get_all_text(self,print_result=False):            
        self.cursor.execute('SELECT paragraph, content FROM content_train ORDER BY paragraph')
        texts_table = self.cursor.fetchall()
        
        if print_result:
            for text in texts_table:
                print "%3s %s" % text
         
        return texts_table
        
    def get_all_text_tag_line_item(self,print_result=False):            
        self.cursor.execute('SELECT paragraph,tag FROM tag_train ORDER BY paragraph')
        texttags_table = self.cursor.fetchall()
        
        if print_result:
            for texttag in texttags_table:
                print "%02d %02d" % texttag
         
        return texttags_table
    
    def read_text_tag(self,print_result=False):            
        texts = self.get_all_text()
        texts_tags = self.get_all_text_tag_line_item()
        
        result_text_id = []
        result_text_content = []
        result_tag = []
        
        for text in texts:
            if len(text[1]) < 200:
                continue
            
            text_id = text[0]
            tags = []
            for text_tag in texts_tags:
                if text_tag[0] == text_id:
                    tags.append(str(text_tag[1]))
            
            result_text_id.append(int(text[0]))
            result_text_content.append(text[1])
            result_tag.append(",".join(list(set(tags))))
        
        if print_result:
            for i in range(0,len(result_text_id)):
                print "%d) %s %s" % (result_text_id[i],result_tag[i],result_text_content[i])
        
        return result_text_id,result_text_content,result_tag
                    
if __name__ == '__main__':
    #ImpalaDB().get_all_tag(True)
    #ImpalaDB().get_all_text(True)
    
    from collections import defaultdict
    frequency = defaultdict(int)
    
    for data in ImpalaDB().read_text_tag(False)[2]:
        data = data.split(',')
        for tag in data:
            if tag.strip():
                frequency[int(tag)] += 1
    
    import operator
    sorted_frequency = sorted(frequency.items(), key=operator.itemgetter(1), reverse=True)
    for row in sorted_frequency:
        print row[0],row[1]
