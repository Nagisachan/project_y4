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
        $stmt = $this->em->getConnection()->prepare("select f.file_id,f.file_name as name, substring(string_agg(c.content,' ') from 0 for 50) || '...' as content, count(t.tag) as tags from file f left join content c on f.file_id = c.file_id left join tag t on f.file_id = t.file_id where upper(f.status)='A' group by f.file_id, f.file_name");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getUntaggedParagraph($fileId){
        $stmt = $this->em->getConnection()->prepare("select f.file_id, f.file_name, c.paragraph_id, c.content, f.file_uploaded_date from content c join file f on c.file_id=f.file_id where c.file_id=:file_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getFilename($fileId){
        $stmt = $this->em->getConnection()->prepare("select file_name from file where file_id=:file_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();
        $row = $stmt->fetchAll();

        return ($row ? $row[0]['file_name'] : null);
    }

    public function getTagStructure(){
        $stmt = $this->em->getConnection()->prepare("select c.id as category_id, c.name as category_name, c.color as category_color, c.created_date as category_created_data, c.id::text || i.item::text as tag_id, i.name as tag_name, i.created_date as tag_created_date from tag_category c left join tag_category_item i on c.id = i.category_id where upper(c.status)='A' and upper(i.status)='A' order by category_id,tag_id");
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
}