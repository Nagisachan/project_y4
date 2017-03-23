<?php

namespace AppBundle\Model;

class DB
{
    function __construct($em) {
        $this->em = $em;
    }

    public function writeToFileTable($file_name){
        $stmt = $this->em->getConnection()->prepare("INSERT INTO file (file_name) VALUES(:file_name) RETURNING file_id");
        $stmt->bindValue(':file_name',$file_name);
        $file_id = $stmt->execute();

        return $file_id;
    }

    public function writeToContentTable($file_id,$paragraph_id,$content){
        $stmt = $this->em->getConnection()->prepare("INSERT INTO content (file_id,paragraph_id,content) VALUES(:file_id,:paragraph_id,:content) RETURNING content_id");
        $stmt->bindValue(':file_id',$file_id);
        $stmt->bindValue(':paragraph_id',$paragraph_id);
        $stmt->bindValue(':content',$content);
        $content_id = $stmt->execute();

        return $content_id;
    }
}