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
        $content_id = $stmt->execute();

        return $content_id;
    }

    public function getUntaggedDocument(){
        $stmt = $this->em->getConnection()->prepare("select f.file_id,f.file_name as name, substring(string_agg(c.content,' ') from 0 for 50) || '...' as content, count(t.tag) as tags from file f left join content c on f.file_id = c.file_id left join tag t on f.file_id = t.file_id where upper(f.status)='A' group by f.file_id, f.file_name");
        $stmt->execute();

        return $stmt->fetchAll();
    }
}