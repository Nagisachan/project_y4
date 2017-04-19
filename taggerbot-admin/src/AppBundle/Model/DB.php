<?php

namespace AppBundle\Model;

class DB
{
    function __construct($em,$logger) {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function writeToFileTable($file_name){
        $stmt = $this->em->getConnection()->prepare("INSERT INTO file (file_name) VALUES(:file_name) RETURNING file_id");
        $stmt->bindValue(':file_name',$file_name);
        $stmt->execute();
        $file_id = $stmt->fetchAll();

        return $file_id[0]['file_id'];
    }

    public function writeToContentTable($file_id,$paragraph_id,$content){
        $stmt = $this->em->getConnection()->prepare("INSERT INTO content (file_id,paragraph_id,content) VALUES(:file_id,:paragraph_id,:content) RETURNING content_id");
        $stmt->bindValue(':file_id',$file_id);
        $stmt->bindValue(':paragraph_id',$paragraph_id);
        $stmt->bindValue(':content',$content);
        $stmt->execute();
        $content_id = $stmt->fetchAll();

        return $content_id[0]['content_id'];
    }

    public function getUntaggedDocument(){
        $stmt = $this->em->getConnection()->prepare("select f.file_id, f.file_name as name, substring(string_agg(c.content,',') from 0 for 100) || '...' as content from file f left join content c on f.file_id = c.file_id left join tag t on f.file_id = t.file_id and c.paragraph_id = t.paragraph_id where upper(f.status)='A' and t.tag is null group by f.file_id order by f.file_id");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getUntaggedParagraph($fileId){
        $stmt = $this->em->getConnection()->prepare("select f.file_id, f.file_name, c.paragraph_id, string_agg(t.tag,',') as tags, c.content, f.file_uploaded_date from content c join file f on c.file_id=f.file_id left join tag t on f.file_id = t.file_id and c.paragraph_id = t.paragraph_id where c.file_id=:file_id group by f.file_id, c.paragraph_id, c.content order by c.paragraph_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();

        $data = $stmt->fetchAll();
        for($i=0;$i<count($data);$i++){
            if($data[$i]['tags'] != null){
                $data[$i]['tags'] = preg_split('/,/',$data[$i]['tags']);
            }
        }

        return $data;
    }

    public function addTagToParagraph($fileId,$paragraphId,$tag,$isManual=true){
        $stmt = $this->em->getConnection()->prepare("INSERT INTO tag (file_id,paragraph_id,type,tag) VALUES(:file_id,:paragraph_id,:type,:tag)");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->bindValue(':paragraph_id',$paragraphId);
        $stmt->bindValue(':type',$isManual ? 'M' : 'A');
        $stmt->bindValue(':tag',$tag);
        
        $stmt->execute();
    }

    public function getFilename($fileId){
        $stmt = $this->em->getConnection()->prepare("select file_name from file where file_id=:file_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();
        $row = $stmt->fetchAll();

        return ($row ? $row[0]['file_name'] : null);
    }

    public function getTagStructure(){
        $stmt = $this->em->getConnection()->prepare("select c.id as category_id, c.name as category_name, c.color as category_color, c.created_date as category_created_data, c.id::text || '-' || i.item::text as tag_id, i.name as tag_name, i.created_date as tag_created_date from tag_category c left join tag_category_item i on c.id = i.category_id where upper(c.status)='A' and upper(i.status)='A' order by category_id,tag_id");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $res = array();

        foreach($rows as $row){
            for($i=0;$i<count($res);$i++){
                if($res[$i]['category_id'] == $row['category_id']){
                    break;
                }
            }

            if($i == count($res)){
                // not found
                $res[] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'category_created_data' => $row['category_created_data'],
                    'category_color' => $row['category_color'],
                    'tags' => array(),
                );
            }

            $res[$i]['tags'][] = array(
                'tag_id' => $row['tag_id'],
                'tag_name' => $row['tag_name'],
                'tag_created_date' => $row['tag_created_date'],
            );
        }

        return $res;
    }

    public function createCategory($categoryName,$categoryIdColor=false,$categoryId=false){
        $id = $categoryId ? ':id' : 'DEFAULT';
        $color = $categoryIdColor ? ':color' : 'DEFAULT';

        $stmt = $this->em->getConnection()->prepare("INSERT INTO tag_category (id,name,color) VALUES($id,:name,$color) RETURNING id");
        $stmt->bindValue(':name',$categoryName);
        
        if($categoryId){
            $stmt->bindValue(':id',$categoryId);
        }
        
        if($categoryIdColor){
            $stmt->bindValue(':color',$categoryIdColor);
        }

        $stmt->execute();
        $id = $stmt->fetchAll()[0]['id'];

        return $id;
    }

    public function createTag($categoryId,$name){
        // find current max nItem
        $stmt = $this->em->getConnection()->prepare("SELECT max(item) AS max FROM tag_category_item WHERE category_id=:category_id");
        $stmt->bindValue(':category_id',$categoryId);
        $maxItem = $stmt->execute();
        
        if($maxItem){
            $maxItem =  $stmt->fetchAll();
            $maxItem = $maxItem[0]['max'];
        }
        else{
            $maxItem = 0;
        }

        $maxItem += 1;

        $stmt = $this->em->getConnection()->prepare("INSERT INTO tag_category_item (category_id,item,name) VALUES(:category_id,:item,:name) RETURNING item");
        $stmt->bindValue(':category_id',$categoryId);
        $stmt->bindValue(':item',$maxItem);
        $stmt->bindValue(':name',$name);
        $stmt->execute();
        $item = $stmt->fetchAll()[0]['item'];

        return $item;
    }

    public function getTagCount(){
        $stmt = $this->em->getConnection()->prepare("select count(*) as n from tag_category_item");
        $stmt->execute();
        $item = $stmt->fetchAll()[0]['n'];

        return $item;
    }

    public function getTagParagraph($tagId){
        $stmt = $this->em->getConnection()->prepare("select t.tag as tag_id, t.file_id as file_id, t.paragraph_id as paragraph_id, f.file_name as file_name, string_agg(i.name,', ') as tags, c.content as content from tag t join content c on t.file_id=c.file_id and t.paragraph_id=c.paragraph_id join file f on c.file_id=f.file_id join tag t2 on c.file_id=t2.file_id and c.paragraph_id=t2.paragraph_id join tag_category_item i on t2.tag = (i.category_id || '-' || i.item) where t.tag=:tag_id group by t.tag, f.file_name, t.file_id, t.paragraph_id, c.content order by f.file_name");
        $stmt->bindValue(':tag_id',$tagId);
        $stmt->execute();
        $item = $stmt->fetchAll();

        return $item;
    }

    public function getAllParagraph($fileId){
        $stmt = $this->em->getConnection()->prepare("select * from content where file_id=:file_id and status='A'");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return $items;
    }

    public function getAllText(){
        $stmt = $this->em->getConnection()->prepare("select content from content");
        $stmt->execute();
        $items = $stmt->fetchAll();

        $text = array();
        foreach($items as $item){
            $item['content'] = trim($item['content']);
            $text[] = $item;
        }

        return $text;
    }

    public function getContent($fileId,$paragraphId){
        $stmt = $this->em->getConnection()->prepare("SELECT content FROM content WHERE status='A' AND file_id=:file_id AND paragraph_id=:paragraph_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->bindValue(':paragraph_id',$paragraphId);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        if($item){
            return trim($item[0]['content']);
        }
        else{
            return "";
        }
        
    }

    public function getContentsNotTag($tagId,$n){
        $stmt = $this->em->getConnection()->prepare("SELECT c.content FROM content c left join tag t on t.file_id = c.file_id and t.paragraph_id=t.paragraph_id WHERE c.status='A' and t.tag != :tag_id group by c.content");
        $stmt->bindValue(':tag_id',$tagId);
        $stmt->execute();
        $items = $stmt->fetchAll();
        
        $keys = array_rand($items,$n);

        $text = array();
        foreach($keys as $key){
            $item = trim($items[$key]['content']);
            $text[] = $item;
        }

        return $text;
    }

    public function getModels(){
        $stmt = $this->em->getConnection()->prepare("select * from model where status='A'");
        $stmt->execute();
        $items = $stmt->fetchAll();

        return $items;
    }

    public function getModelInfo(){
        $stmt = $this->em->getConnection()->prepare("select tag_id, name, information from model m join tag_category_item t on t.category_id::text=split_part(tag_id, '-', 1) and t.item::text=split_part(tag_id, '-', 2)"); /*  where m.status='A' */
        $stmt->execute();
        $items = $stmt->fetchAll();

        for($i=0;$i<count($items);$i++){
            $items[$i]['information'] = json_decode($items[$i]['information']);
        }

        return $items;
    }

    public function getAllTagTypeCount(){
        $stmt = $this->em->getConnection()->prepare("select type, count(*) from tag where status='A' group by type");
        $stmt->execute();
        $items = $stmt->fetchAll();

        return $items;
    }

    public function getTagAssocDataCount(){
        $stmt = $this->em->getConnection()->prepare("select name, count(*) from tag join tag_category_item t on t.category_id::text=split_part(tag, '-', 1) and t.item::text=split_part(tag, '-', 2) where tag.status='A' group by name, file_id");
        $stmt->execute();
        $items = $stmt->fetchAll();

        return $items;
    }
}