<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Model\FilePreprocessor;
use AppBundle\Model\DB;

class ServiceController extends Controller
{
    public function uploadFileAction(Request $request){
        $success = true;
        $files = array();
        $preprocessor = new FIlePreprocessor($this->get('logger'));

        // inspect $_FILES structure
        $this->get('logger')->debug(json_encode($_FILES));

        // for uploading via drap-and-drop 
        foreach($_FILES as $key => $value){
            if(gettype($value['name']) == "string"){
                $this->get('logger')->debug($value['tmp_name']);
                $output_file = $preprocessor->toText($value['tmp_name']);
                $paragraphs = $preprocessor->toParagraph($output_file);
                $name = $value['name'];

                $files[] = array(
                    'name' => $name,
                    'text' => $paragraphs
                );
            }
        }

        // for uploading via normal input
        if(count($_FILES['files']['name']) > 0){
            for($i=0;$i<count($_FILES['files']['name']);$i++){
                if($_FILES['files']['name'][$i] == ""){
                    continue;
                }

                $output_file = $preprocessor->toText($_FILES['files']['tmp_name'][$i]);
                $paragraphs = $preprocessor->toParagraph($output_file);
                $name = $_FILES['files']['name'][$i];

                $files[] = array(
                    'name' => $name,
                    'text' => $paragraphs
                );
            }
        }

        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        foreach($files as $file){
            $file_id = $db->writeToFileTable($file['name']);

            for($i=0;$i<count($file['text']);$i++){
                $db->writeToContentTable($file_id,$i,$file['text'][$i]);
            }
        }

        return $this->buildSuccessJson($files);
    }
    
    public function untaggedAction()
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $docs = $db->getUntaggedDocument();
        $targetDocs = array();
        foreach($docs as $doc){
            if($doc['tags'] == 0){
                $targetDocs[] = $doc;
            }
        }
        
        return $this->buildSuccessJson($targetDocs);
    }

    public function untaggedFileAction($fileId)
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $docs = $db->getUntaggedParagraph($fileId);
        
        return $this->buildSuccessJson($docs);
    }

    public function tagStructureAction()
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $tags = $db->getTagStructure();
        
        return $this->buildSuccessJson($tags);
    }

    public function updateTagStructureAction(Request $request)
    {
        $data = $request->request->get('json_data', '{}');
        $data = json_decode($data);
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));

        foreach($data as $category){
            $category = (array)$category;

            // new category
            if($category['category_id'] == null){
                //create it and it's tags
                if(isset($category['category_name']) && trim($category['category_name']) != ""){
                    $categoryName = trim($category['category_name']);
                    $categoryColor = isset($category['category_color']) && trim($category['category_color']) != "" ? trim($category['category_color']) : false;

                    $categoryId = $db->createCategory($categoryName,$categoryColor);

                    foreach($category['data'] as $tag){
                        $tag = (array)$tag;
                        $db->createTag($categoryId,$tag['text']);
                    }
                }
                

            }
        }

        
        
        return $this->buildSuccessJson($data);
    }

    function buildSuccessJson($data){
        return new JsonResponse(array(
            'success' => true,
            'data' => $data
        ));
    }
}
