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
                $extension = "";
                if($this->isHasExtension($value['name'],'PDF')){
                    $paragraphs = $this->preprocess($value['tmp_name']);
                    $extension = "pdf";
                }
                else if($this->isHasExtension($value['name'],'DOCX')){
                    $paragraphs = $this->preprocessDocx($value['tmp_name']);
                    $extension = "docx";
                }
                
                $name = $value['name'];

                $files[] = array(
                    'name' => $name,
                    'tmp_name' => $value['tmp_name'],
                    'extension' => $extension,
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
                
                $extension = "";
                if($this->isHasExtension($_FILES['files']['name'][$i],'PDF')){
                    $paragraphs = $this->preprocess($_FILES['files']['tmp_name'][$i]);
                    $extension = "pdf";
                }
                else if($this->isHasExtension($_FILES['files']['name'][$i],'DOCX')){
                    $paragraphs = $this->preprocessDocx($_FILES['files']['tmp_name'][$i]);
                    $extension = "docx";
                }

                $name = $_FILES['files']['name'][$i];

                $files[] = array(
                    'name' => $name,
                    'tmp_name' => $_FILES['files']['tmp_name'][$i],
                    'extension' => $extension,
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

            // $targetPath = $this->get('kernel')->getRootDir() . "/../web/assets/files/";
            $targetPath = $this->get('kernel')->getRootDir() . "/../web/assets/files/dataset/";
            $targetPath = sprintf("%s%s/%s_%s",$targetPath,$file['extension'],$file_id,$file['name']);
            rename($file['tmp_name'],$targetPath);
            chmod($targetPath,0666);
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

    public function allDocumentAction()
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $docs = $db->getDocument();
        
        return $this->buildSuccessJson($docs);
    }

    public function removeDocumentAction($id)
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $doc = $db->removeDocument($id);
        
        return $this->buildSuccessJson($doc);
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

        $db->clearTagOfParagraph($fileId,$paragraphId);
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

    public function updateTagsAction(Request $request,$tagId){
        $data = $request->request->get('paragraph_ids', '[]');
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $db->removeTagByParagraphIds($tagId,$data);

        return $this->buildSuccessJson('done');
    }

    public function updateTagStructureAction(Request $request)
    {
        $data = $request->request->get('json_data', '{}');
        $data = json_decode($data);
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));

        // check missing, then set status as inactive
        $dbTags = $db->getTagStructure();
        foreach($dbTags as $tag){
            $found = false;
            for($i=0;$i<count($data);$i++){
                $newTag = (array)$data[$i];
                if($tag['category_id'] == $newTag['category_id']){
                    $found = true;
                    break;
                }
            }

            if(!$found){
                $this->get('logger')->debug("Not found (deleted) tag: " . $tag['category_id']);
                $db->disableTag($tag['category_id']);
            }
        }

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
            // existing tag
            else{
                $id = $category['category_id'];
                $color = $category['category_color'];
                $dbTag = $this->findTagById($dbTags,$id);
                
                // check color
                if($dbTag['category_color'] != $color){
                    $db->updateTagColor($id,$color);
                }

                // add tag item if it is not exist
                foreach($category['data'] as $tagItem){
                    $tagItem = (array)$tagItem;
                    
                    if(count(preg_split('/-/',$tagItem['value'])) != 2){
                        $db->addTagItem($id,$tagItem['text']);
                    }
                }

                // check missing tag item of this category
                $dbTagItems = $dbTag['tags'];
                $this->get('logger')->debug(json_encode($dbTagItems));
                for($i=0;$i<count($dbTagItems);$i++){
                    //  extracy tag item id from combination of tag caegory and tag item id, eg. 44-1 
                    $dbTagItemId = preg_split('/-/',$dbTagItems[$i]['tag_id'])[1];
                    $this->get('logger')->debug("DB TO CHECK:" . $dbTagItemId);

                    // compare tag items from user with tag item in database
                    $found = false;
                    foreach($category['data'] as $tag){
                        $tag = (array)$tag;
                        $tmp = preg_split('/-/',$tag['value']);
                        
                        if(count($tmp) != 2){
                            // new tag item
                            continue;
                        }
                        
                        $tagItemId = $tmp[1];

                        $this->get('logger')->debug("COMPARE:" . $dbTagItemId . "," . $tagItemId);
                        if($dbTagItemId == $tagItemId){
                            $found = true;
                            break;
                        }
                    }

                    if(!$found){
                        $this->get('logger')->debug("REMOVE:" . $id . "," . $dbTagItemId);
                        $db->disableTagItem($id,$dbTagItemId);
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
        $db->clearAutoTag($fileId);
        $models = $db->getModels();
        $paragraphs = $db->getAllParagraph($fileId);
        $ml = new ML($this->get('logger'));
        $scores = array();

        $paragraphsIds = [];
        foreach($paragraphs as $paragraph){
            $paragraphsIds[] = $paragraph['fpid'];
        }

        foreach($models as $model){
            $url = $model['url'];
            $key = $model['key'];

            $classes = $ml->predict($url,$paragraphs);
            for($i=0;$i<count($classes);$i++){
                $classes[$i] = $classes[$i] == "0" ? 0 : 1;
            }

            $scores[] = array(
                'tagId' => $model['tag_id'],
                'class' => $classes, //array_combine($paragraphsIds,$classes),
            );
        }

        $ret = array();

        foreach($scores as $score){
            $tagId = $score['tagId'];
            $score = $score['class'];

            for($i=0;$i<count($score);$i++){
                $tag = $score[$i];
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

        return $this->buildSuccessJson($scores);
    }

    public function predictAzureAction($fileId){
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

    public function documentAndParagraphGrowthAction(){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        
        $result = array();
        $docGrowth = $db->getDocumentGrowth();
        $paragraphGrowth = $db->getParagraphGrowth();

        foreach($docGrowth as $docRecord){
            $result[$docRecord['date']] = array(
                'doc' => intval($docRecord['n'])
            );
        }
        
        foreach($paragraphGrowth as $paragraphRecord){
            $result[$paragraphRecord['date']]['paragraph'] = intval($paragraphRecord['n']);
        }

        return $this->buildSuccessJson($result);
    }

    public function getSchoolsAction(Request $request){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $page = $request->query->get('page', 0);
        $step = $request->query->get('step', 0);
        $data = $db->getSchool($page,$step);
        
        return $this->buildSuccessJson($data);
    }

    public function deleteSchoolAction($id){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $id = $db->deleteSchool($id);
        return $this->buildSuccessJson($id);
    }

    public function updateSchoolAction(Request $request, $id){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));

        $name = $request->request->get('name', null);
        $lat = $request->request->get('lat', 0);
        $lon = $request->request->get('lon', 0);
        $location = $request->request->get('location', '');
        $tel = $request->request->get('tel', '');
        $website = $request->request->get('website', '');
        $information = $request->request->get('information', '');

        if($name == null){
            return $this->buildErrorJson('Need at least a school name.');
        }

        $id = $db->updateSchool($id,$name,$lat,$lon,$location,$tel,$website,$information);
        return $this->buildSuccessJson($id);
    }

    public function addSchoolAction(Request $request){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));

        $name = $request->request->get('name', null);
        $lat = $request->request->get('lat', 0);
        $lon = $request->request->get('lon', 0);
        $location = $request->request->get('location', '');
        $tel = $request->request->get('tel', '');
        $website = $request->request->get('website', '');
        $information = $request->request->get('information', '');

        if($name == null){
            return $this->buildErrorJson('Need at least a school name.');
        }

        $id = $db->addSchool($name,$lat,$lon,$location,$tel,$website,$information);
        return $this->buildSuccessJson($id);
    }

    public function searchSchoolAction($query){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $result = $db->searchSchool($query);

        return $this->buildSuccessJson($result);
    }

    public function trainAllAction(){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        if($db->lockTrain(true) === false){
            return $this->buildErrorJson('can not lock');
        }

        $cmd = 'python production/train.py 2>&1';
        $output = array();
        exec($cmd,$output);
        $db->lockTrain(false);
        return $this->buildSuccessJson($output);
    }

    public function buildCorpusAction(){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        if($db->lockTrain(true) === false){
            return $this->buildErrorJson('can not lock');
        }

        $cmd = 'python production/build_text_transformer.py 2>&1';
        $output = array();
        exec($cmd,$output);
        $db->lockTrain(false);

        return $this->buildSuccessJson($output);
    }

    public function removeParagraphAction($fid,$pid){
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $id = $db->removeParagraph($fid,$pid);

        return $this->buildSuccessJson($id);
    }

    /* Internal functions */

    function buildSuccessJson($data){
        return new JsonResponse(array(
            'success' => true,
            'data' => $data
        ));
    }

    function buildErrorJson($data){
        return new JsonResponse(array(
            'success' => false,
            'data' => $data
        ));
    }

    function isHasExtension($file,$ext){
        if($ext[0] !== '.'){
            $ext = '.' . $ext;
        }

        return strtoupper(substr($file, -strlen($ext))) === strtoupper($ext);
    }

    function findTagById($tags,$id){
        foreach($tags as $tag){
            $ary = (array)$tag;
            if($ary['category_id'] == $id){
                return (array)$tag;
            }
        }

        return false;
    }
}
