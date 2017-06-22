<?php

namespace AppBundle\Model;

class DB
{
    function __construct($em,$logger) {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function writeToFileTable($file_name,$school=null){
        $stmt = $this->em->getConnection()->prepare("INSERT INTO file (file_name,school) VALUES(:file_name,:school) RETURNING file_id");
        $stmt->bindValue(':file_name',$file_name);
        $stmt->bindValue(':school',$school);
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

    public function getDocument(){
        $stmt = $this->em->getConnection()->prepare("select f.file_id, f.file_name as name, substring(string_agg(c.content,',') from 0 for 100) || '...' as content from file f left join content c on f.file_id = c.file_id where upper(f.status)='A' group by f.file_id order by f.file_id");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getDocumentCount(){
        $stmt = $this->em->getConnection()->prepare("select count(*) as n from file where status='A'");
        $stmt->execute();
        $n = intval($stmt->fetchAll()[0]['n']);

        return $n;
    }

    public function removeDocument($id){
        $stmt = $this->em->getConnection()->prepare("update file set status='I' where file_id=:id returning file_id");
        $stmt->bindValue(':id',$id);
        $stmt->execute();
        $n = intval($stmt->fetchAll()[0]['file_id']);

        return $n;
    }

    public function getUntaggedParagraph($fileId){
        $stmt = $this->em->getConnection()->prepare("select f.file_id, f.file_name, c.paragraph_id, string_agg(t.tag,',') as tags, string_agg(item.name,',') as tag_texts, c.content, f.file_uploaded_date from content c join file f on c.file_id=f.file_id left join tag t on f.file_id = t.file_id and c.paragraph_id = t.paragraph_id left join tag_category_item item on t.tag = item.category_id || '-' || item.item where c.status='A' and c.file_id=:file_id group by f.file_id, c.paragraph_id, c.content order by c.paragraph_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();

        $data = $stmt->fetchAll();

        // tag id (string) to array eg. "44-1,44-2" => ["44-1","44-2"]
        for($i=0;$i<count($data);$i++){
            if($data[$i]['tags'] != null){
                $data[$i]['tags'] = preg_split('/,/',$data[$i]['tags']);
            }
        }

        // tag name (string) to array "A,B" => ["A","B"]
        for($i=0;$i<count($data);$i++){
            if($data[$i]['tag_texts'] != null){
                $data[$i]['tag_texts'] = preg_split('/,/',$data[$i]['tag_texts']);
            }
        }

        return $data;
    }

    public function clearTagOfParagraph($fileId,$paragraphId){
        $stmt = $this->em->getConnection()->prepare("DELETE FROM tag WHERE file_id=:file_id AND paragraph_id=:paragraph_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->bindValue(':paragraph_id',$paragraphId);
        
        $stmt->execute();
    }

    public function addTagToParagraph($fileId,$paragraphId,$tag,$isManual=true){
        $stmt = $this->em->getConnection()->prepare("INSERT INTO tag (file_id,paragraph_id,type,tag) VALUES(:file_id,:paragraph_id,:type,:tag)");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->bindValue(':paragraph_id',$paragraphId);
        $stmt->bindValue(':type',$isManual ? 'M' : 'A');
        $stmt->bindValue(':tag',$tag);
        
        $stmt->execute();
    }

    public function removeTagByParagraphIds($tagId,$paragraphIds){
        foreach($paragraphIds as $id){
            $tmp = preg_split('/-/',$id);
            $fid = $tmp[0];
            $pid = $tmp[1];

            $stmt = $this->em->getConnection()->prepare("update tag set status='I' where file_id=:file_id and paragraph_id=:paragraph_id and tag=:tag_id");
            $stmt->bindValue(':file_id',$fid);
            $stmt->bindValue(':paragraph_id',$pid);
            $stmt->bindValue(':tag_id',$tagId);
            $stmt->execute();
        }
    }

    public function getFilename($fileId){
        $stmt = $this->em->getConnection()->prepare("select file_name from file where file_id=:file_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();
        $row = $stmt->fetchAll();

        return ($row ? $row[0]['file_name'] : null);
    }

    public function getTagStructure(){
        $stmt = $this->em->getConnection()->prepare("select c.id as category_id, c.name as category_name, c.color as category_color, c.created_date as category_created_data, c.id::text || '-' || i.item::text as tag_id, i.name as tag_name, i.created_date as tag_created_date from tag_category c left join tag_category_item i on c.id = i.category_id where upper(c.status)='A' and upper(i.status)='A' order by category_id,tag_created_date");
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

    public function disableTag($id){
        $stmt = $this->em->getConnection()->prepare("update tag_category set status='I' where id=:id");
        $stmt->bindValue(':id',$id);
        $stmt->execute();

        $stmt = $this->em->getConnection()->prepare("update tag_category_item set status='I' where category_id=:id");
        $stmt->bindValue(':id',$id);
        $stmt->execute();
    }

    public function disableTagItem($categoryId,$itemId){
        $stmt = $this->em->getConnection()->prepare("update tag_category_item set status='I' where category_id=:id and item=:item_id");
        $stmt->bindValue(':id',$categoryId);
        $stmt->bindValue(':item_id',$itemId);
        $stmt->execute();
    }

    public function addTagItem($categoryId,$itemName){
        $stmt = $this->em->getConnection()->prepare("select max(item) as n from tag_category_item where category_id=:id");
        $stmt->bindValue(':id',$categoryId);
        $stmt->execute();
        $maxItem = intval($stmt->fetchAll()[0]['n']);

        $stmt = $this->em->getConnection()->prepare("insert into tag_category_item values(:id,:item,:name,DEFAULT,DEFAULT)");
        $stmt->bindValue(':id',$categoryId);
        $stmt->bindValue(':item',$maxItem+1);
        $stmt->bindValue(':name',$itemName);
        $stmt->execute();
    }

    public function updateTagColor($id,$color){
        $stmt = $this->em->getConnection()->prepare("update tag_category set color=:color where id=:id");
        $stmt->bindValue(':id',$id);
        $stmt->bindValue(':color',$color);
        $stmt->execute();
    }

    public function updateTagItem($id){
        $stmt = $this->em->getConnection()->prepare("update tag_category set status='I' where id=:id");
        $stmt->bindValue(':id',$id);
        $stmt->execute();

        $stmt = $this->em->getConnection()->prepare("update tag_category_item set status='I' where category_id=:id");
        $stmt->bindValue(':id',$id);
        $stmt->execute();
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
        $stmt = $this->em->getConnection()->prepare("select t.tag as tag_id, t.file_id as file_id, t.paragraph_id as paragraph_id, f.file_name as file_name, string_agg(i.name,', ') as tags, c.content as content from tag t join content c on t.file_id=c.file_id and t.paragraph_id=c.paragraph_id join file f on c.file_id=f.file_id join tag t2 on c.file_id=t2.file_id and c.paragraph_id=t2.paragraph_id join tag_category_item i on t2.tag = (i.category_id || '-' || i.item) where t.tag=:tag_id and t.status='A' group by t.tag, f.file_name, t.file_id, t.paragraph_id, c.content order by f.file_name");
        $stmt->bindValue(':tag_id',$tagId);
        $stmt->execute();
        $item = $stmt->fetchAll();

        return $item;
    }

    public function getAllParagraph($fileId){
        $stmt = $this->em->getConnection()->prepare("select *, file_id || '-' || paragraph_id as fpid from content where file_id=:file_id and status='A'");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return $items;
    }

    public function removeParagraph($fileId,$paragraphId){
        $stmt = $this->em->getConnection()->prepare("update content set status='I' where file_id=:file_id and paragraph_id=:paragraph_id returning paragraph_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->bindValue(':paragraph_id',$paragraphId);
        $stmt->execute();
        $item = $stmt->fetchAll()['0']['paragraph_id'];

        return $item;
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
        $stmt = $this->em->getConnection()->prepare("select tag_id, name, information from model m left join tag_category_item t on t.category_id::text=split_part(tag_id, '-', 1) and t.item::text=split_part(tag_id, '-', 2) where m.status='A'");
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

    public function getSchool($page=0,$step=11){
        // $stmt = $this->em->getConnection()->prepare("select gid,name,status,st_x(the_geom) as lon, st_y(the_geom) as lat, location, tel, website, information from school where status='A'");
        // $stmt = $this->em->getConnection()->prepare("select id as gid, name,st_x(the_geom) as lon, st_y(the_geom) as lat, subdistrict as location, telephone as tel, website, type as information from school_all a left join file f on a.id=f.school where f.school is not null group by gid");
        
        $stmt = $this->em->getConnection()->prepare("select id as gid, name,st_x(the_geom) as lon, st_y(the_geom) as lat, district || ' ' || subdistrict as location, telephone as tel, website, type as information from school_all order by gid " . (($page > 0 && $step > 0) ? 'limit ' . (($page-1)*$step + $step) : ''));
        $stmt->execute();
        $items = $stmt->fetchAll();

        $stmt = $this->em->getConnection()->prepare("select count(*) as n from school_all");
        $stmt->execute();
        $total = $stmt->fetchAll()[0]['n'];

        if($page > 0 && $step > 0 && ($page-1)*$step < count($items)){
            $items = array_slice($items, ($page-1)*$step, $step);
            $nPage = ceil($total/$step);
        }
        else{
            $nPage = ceil($total/$step);
        }

        return array(
            'data' => $items,
            'total_page' => $nPage
        );
    }

    public function getSchoolCount(){
        $stmt = $this->em->getConnection()->prepare("select count(*) as n from school_all");
        $stmt->execute();
        $total = $stmt->fetchAll()[0]['n'];

        return $total;
    }

    public function deleteSchool($id){
        $stmt = $this->em->getConnection()->prepare("update school set status='I' where gid=:id returning gid");
        $stmt->bindValue(':id',$id);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return $items[0]['gid'];
    }

    public function updateSchool($id,$name,$lat,$lon,$location,$tel,$website,$information){
        $stmt = $this->em->getConnection()->prepare("update school set name=:name,the_geom=st_setsrid(st_makepoint(:lon,:lat),4326), location=:location, tel=:tel, website=:website, information=:information where gid=:id returning gid");
        $stmt->bindValue(':name',$name);
        $stmt->bindValue(':lon',$lon);
        $stmt->bindValue(':lat',$lat);
        $stmt->bindValue(':location',$location);
        $stmt->bindValue(':tel',$tel);
        $stmt->bindValue(':website',$website);
        $stmt->bindValue(':information',$information);
        $stmt->bindValue(':id',$id);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return $items[0]['gid'];
    }

    public function addSchool($name,$lat,$lon,$location,$tel,$website,$information){
        $stmt = $this->em->getConnection()->prepare("insert into school values(DEFAULT,:name,DEFAULT,st_setsrid(st_makepoint(:lon,:lat),4326),:location,:tel,:website,:information) returning gid");
        $stmt->bindValue(':name',$name);
        $stmt->bindValue(':lon',$lon);
        $stmt->bindValue(':lat',$lat);
        $stmt->bindValue(':location',$location);
        $stmt->bindValue(':tel',$tel);
        $stmt->bindValue(':website',$website);
        $stmt->bindValue(':information',$information);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return $items[0]['gid'];
    }

    public function clearAutoTag($fileId){
        $stmt = $this->em->getConnection()->prepare("DELETE FROM tag WHERE type='A' AND file_id = :file_id");
        $stmt->bindValue(':file_id',$fileId);
        $stmt->execute();
    }

    public function lockTrain($lock=true){
        $key = "train-model-lock";

        $stmt = $this->em->getConnection()->prepare("select * from setting where key='$key'");
        $stmt->execute();
        $items = $stmt->fetchAll();

        if($lock){
            if(count($items) == 0){
                $stmt = $this->em->getConnection()->prepare("insert into setting values('$key','true')");
                $stmt->execute();
                return true;
            }
            else if($items[0]['value'] == 'false'){
                $stmt = $this->em->getConnection()->prepare("update setting set value='true' where key = '$key'");
                $stmt->execute();
                return true;
            }   
            else{
                return false;
            }
        }
        else{
            if(count($items) == 0){
                $stmt = $this->em->getConnection()->prepare("insert into setting values('$key','false')");
                $stmt->execute();
                return true;
            }
            else if($items[0]['value'] == 'true'){
                $stmt = $this->em->getConnection()->prepare("update setting set value='false' where key = '$key'");
                $stmt->execute();
                return true;
            }   
        }
    }
}