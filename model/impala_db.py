from impala.dbapi import connect

class ImpalaDB(object):
    def __init__(self):
        conn = connect(host='23.97.59.54', port=21050, database='thaiautotag')
        self.cursor = conn.cursor()
        self.tag_dict = False
        
    def get_all_tag(self,print_result=False):            
        self.cursor.execute('SELECT tagid, tagcontent FROM tag_data ORDER BY tagid')
        tags_table = self.cursor.fetchall()
        
        if print_result:
            for tag in tags_table:
                print "%3s %s" % tag
         
        return tags_table
        
    def get_all_text_tag(self,print_result=False):            
        self.cursor.execute('select concat(cast(content_train.filecontentid as string),"-",cast(content_train.paragraph as string)) as paragraph_id,tag, content from content_train left join file_train on content_train.filecontentid=file_train.filecontentid join tag_train on file_train.fileid=tag_train.fileid and content_train.paragraph=tag_train.paragraph order by paragraph_id,tag')
        texttags_table = self.cursor.fetchall()
        
        if print_result:
            for texttag in texttags_table:
                print "%5s %2d %s" % (texttag[0],texttag[1],texttag[2][:20])
         
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
        self.cursor.execute('select concat(cast(content_test.fileid as string),"-",cast(content_test.paragraph as string)) as paragraph_id, content from content_test left join train_status on content_test.fileid=train_status.fileid and content_test.paragraph = train_status.paragraph where train is null or not train')
        texts_table = self.cursor.fetchall()
        
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
            self.cursor.execute('INSERT INTO tag_test (fileid,paragraph,tag) VALUES(%d,%d,%d)' % (int(paragraph_ids[0]),int(paragraph_ids[1]),tag))
        
if __name__ == '__main__':
    #ImpalaDB().get_all_text_tag(True)
    #ImpalaDB().read_text_tag(True)
    ImpalaDB().read_test_text(True)
    
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
    
    #ImpalaDB().write_result("2-15",[1,2,3,4]);
