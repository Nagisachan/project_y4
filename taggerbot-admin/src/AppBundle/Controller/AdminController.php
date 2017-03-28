<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Model\FilePreprocessor;
use AppBundle\Model\DB;

class AdminController extends Controller
{
    /* HTML Page */
    public function mainAction(Request $request)
    {
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $asseturl = "$baseurl/assets";
        
        return $this->render('admin.html.twig',array(
            // 'background_image' => "$asseturl/bg/default.jpg",
            // 'background_image_color' => "#457e79"
        ));
    }

    public function uploadAction()
    {
        return $this->render('upload.html.twig');
    }

    public function tagAction()
    {
        return $this->render('tag.html.twig');
    }

    public function docAction()
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $docs = $db->getUntaggedDocument();
        $targetDocs = array();
        foreach($docs as $doc){
            if($doc['tags'] == 0){
                $doc['n'] = count($targetDocs);
                $targetDocs[] = $doc;
            }
        }
        
        return $this->render('doc.html.twig',array(
            'documents' => $targetDocs,
        ));
    }

    public function trainAction()
    {
        return $this->render('train.html.twig');
    }

    public function dashboardAction()
    {
        return $this->render('dashboard.html.twig');
    }

    public function settingAction()
    {
        return $this->render('setting.html.twig');
    }

    public function fileAction($fileId)
    {
        return $this->render('file.html.twig');
    }

    /* JSON service */

    public function uploadFileAction(Request $request){
        $success = true;
        $files = array();
        $preprocessor = new FIlePreprocessor();

        foreach($_FILES as $key => $value){
            if(gettype($value['name']) == "string"){
                $output_file = $preprocessor->toText($value['tmp_name']);
                $paragraphs = $preprocessor->toParagraph($output_file);
                $name = $value['name'];

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


        return new JsonResponse(array(
            'success' => $success,
            'data' => $files
        ));
    }

    public function queryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare("select * from pg_stat_activity");
        $stmt->execute();

        return new JsonResponse($stmt->fetchAll());
    }
}
