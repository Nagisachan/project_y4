<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Model\FilePreprocessor;
use AppBundle\Model\DB;
use AppBundle\Model\ML;
use AppBundle\Model\CsvResponse;

class ServiceController extends Controller
{
    public function uploadFileAction(Request $request){
        $success = true;
        $files = array();
        $this->preprocessor = new FIlePreprocessor($this->get('logger'));

        $school = $request->request->get('school', null);

        // inspect $_FILES structure
        // $this->get('logger')->debug(json_encode($_FILES));

        // for uploading via drap-and-drop 
        foreach($_FILES as $key => $value){
            if(gettype($value['name']) == "string"){
                if($this->isHasExtension($value['name'],'PDF')){
                    $paragraphs = $this->preprocess($value['tmp_name']);
                }
                else if($this->isHasExtension($value['name'],'DOCX')){
                    $paragraphs = $this->preprocessDocx($value['tmp_name']);
                }
                
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
                
                if($this->isHasExtension($_FILES['files']['name'][$i],'PDF')){
                    $paragraphs = $this->preprocess($_FILES['files']['tmp_name'][$i]);
                }
                else if($this->isHasExtension($_FILES['files']['name'][$i],'DOCX')){
                    $paragraphs = $this->preprocessDocx($_FILES['files']['tmp_name'][$i]);
                }

                $name = $_FILES['files']['name'][$i];

                $files[] = array(
                    'name' => $name,
                    'text' => $paragraphs
                );
            }
        }

        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        foreach($files as $file){
            $file_id = $db->writeToFileTable($file['name'],$school);

            for($i=0;$i<count($file['text']);$i++){
                $db->writeToContentTable($file_id,$i,$file['text'][$i]);
            }
        }

        return $this->buildSuccessJson($files);
    }
    
    public function preprocess($tmpName){
        $outputFile = $this->preprocessor->toText($tmpName);
        $paragraphs = $this->preprocessor->toParagraph($outputFile);

        return $paragraphs;
    }

    public function preprocessDocx($tmpName){
        $outputFile = $this->preprocessor->toTextDocx($tmpName);
        $paragraphs = $this->preprocessor->toParagraphSimple($outputFile);

        return $paragraphs;
    }

    public function uploadCrawlAction(Request $request){
        $url = $request->request->get('url', "");
        $school = $request->request->get('school', null);

        if($url != ""){
            set_time_limit(5*60);

            $preprocessor = new FIlePreprocessor($this->get('logger'));
            $output_file = "/tmp/crawl-" . date("YmdHis") . ".txt";
            $preprocessor->crawlUrl($url,$output_file);

            $handle = fopen($output_file, "r");
            $lines = array();
            while (($line = fgets($handle)) !== false) {
                $lines[] = $line;
            }

            unlink($output_file);

            $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
            
            $file_id = $db->writeToFileTable($url,$school);
            $this->get('logger')->debug("[Crawler] fid=$file_id linenum=" . count($lines));

            for($pid=0,$i=0;$i<count($lines);$i++){
                if(strlen($lines[$i]) > 300){
                    $this->get('logger')->debug("[Crawler] add fid=$file_id, pid=$i, strlen=" . strlen($lines[$i]));
                    $db->writeToContentTable($file_id,$i,$lines[$i]);
                    $pid++;
                }
            }
        }

        return $this->buildSuccessJson($lines);
    }

    public function untaggedAction()
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $docs = $db->getUntaggedDocument();
        
        return $this->buildSuccessJson($docs);
    }

    public function untaggedFileAction($fileId)
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $docs = $db->getUntaggedParagraph($fileId);
        
        return $this->buildSuccessJson($docs);
    }

    public function untaggedParagraphUpdateAction(Request $request,$fileId,$paragraphId){
        $tags = $request->request->get('tags', array());
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));

        foreach($tags as $tag){
            $db->addTagToParagraph($fileId,$paragraphId,$tag);
        }

        return $this->buildSuccessJson(array());
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

    function tagParagraphAction($tagId){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $paragraphs = $db->getTagParagraph($tagId);

        return $this->buildSuccessJson($paragraphs);
    }

    public function trainAction(Request $request, $tagId){
        $paragraphIds = $request->request->get('paragraph_ids', '{}');
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        
        $rows = array();

        foreach($paragraphIds as $paragraphId){
            $tmp = preg_split('/-/',$paragraphId);
            $rows[] = array(
                $db->getContent($tmp[0],$tmp[1]),
                floatval(preg_replace('/-/','.',$tagId)),
            );
        }

        $nPos = count($rows);
        $this->get('logger')->info("N = " . $nPos);

        $nagativeText = $db->getContentsNotTag($tagId,$nPos);
        foreach($nagativeText as $text){
            $rows[] = array(
                $text,
                0,
            );
        }

        return new CsvResponse($rows,array('text','tag'));
    }

    public function predictAction($fileId){
        set_time_limit(10*60);
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $models = $db->getModels();
        $paragraphs = $db->getAllParagraph($fileId);
        $ml = new ML($this->get('logger'));
        $scores = array();

        foreach($models as $model){
            $url = $model['url'];
            $key = $model['key'];

            $scores[] = array(
                'tagId' => $model['tag_id'],
                'scores' => $ml->azureml_predict($url,$key,$paragraphs),
            );
        }

        $ret = array();
        foreach($scores as $score){
            $this->get('logger')->debug('run model');
            $this->get('logger')->debug(json_encode($score['scores']));

            $tagId = $score['tagId'];

            // if(gettype($score['scores']) == 'array'){
                $score = $score['scores']->Results->output1->value->Values;
            // }
            // else if(gettype($score['scores']) == 'object'){
            //     $score = $score['scores']->Results->output1->value->Values;
            // }
            // else{
            //     $this->get('logger')->error('invalid format: ' . gettype($score['scores']));
            //     continue;
            // }
            
            for($i=0;$i<count($score);$i++){
                $tag = $score[$i][0];
                $fileId = $paragraphs[$i]['file_id'];
                $paragraphId = $paragraphs[$i]['paragraph_id'];

                if($tag != 0){
                    $tag = preg_replace('/\./','-',$tag);
                    $tag = $tagId;
                    $ret[] = array(
                        'tag' => $tag,
                        'fileId' => $fileId,
                        'paragraphId' => $paragraphId,
                    );

                    try{
                        $db->addTagToParagraph($fileId,$paragraphId,$tag,false);
                    }
                    catch(Exception $e){
                        $this->get('logger')->debug($e->getMessage());
                    }
                }
            }
        }

        return $this->buildSuccessJson($ret);
    }
    
    public function allTextAction(){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $allText = $db->getAllText();

        return new CsvResponse($allText,array('text'));
    }

    public function modelInfoAction(){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $models = $db->getModelInfo();

        return $this->buildSuccessJson($models);
    }

    public function allTagTypeCountAction(){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $tags = $db->getAllTagTypeCount();

        return $this->buildSuccessJson($tags);
    }

    public function tagAssocDataAction(){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $tags = $db->getTagAssocDataCount();

        return $this->buildSuccessJson($tags);
    }

    /* Internal functions */

    function buildSuccessJson($data){
        return new JsonResponse(array(
            'success' => true,
            'data' => $data
        ));
    }

    function isHasExtension($file,$ext){
        if($ext[0] !== '.'){
            $ext = '.' . $ext;
        }

        return strtoupper(substr($file, -strlen($ext))) === strtoupper($ext);
    }
}
