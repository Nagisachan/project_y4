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
        
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));


        return $this->render('admin.html.twig',array(
            // 'background_image' => "$asseturl/bg/default.jpg",
            // 'background_image_color' => "#457e79",
            'summary' => array(
                'n_category' => count($db->getTagStructure()),
                'n_untagged' => count($db->getUntaggedDocument()),
                'n_untrained' => 0,
                'n_report' => 0,
            ),
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
        return $this->render('doc.html.twig');
    }

    public function trainAction()
    {
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $categories = $db->getTagStructure();
        $tags = [];

        foreach($categories as $category){
            foreach($category['tags'] as $tag){
                $tag['tag_color'] = $category['category_color'];
                $tag['tag_category'] = $category['category_name'];
                $tags[] = $tag;
            }
        }

        return $this->render('train.html.twig',array(
            'tags' => $tags,
        ));
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
        // get file name
        $db = new DB($this->getDoctrine()->getManager(),$this->get('logger'));
        $filename = $db->getFilename($fileId);
        $categories = $db->getTagStructure();

        return $this->render('file.html.twig',array(
            'fileId' => $fileId,
            'filename' => $filename,
            'categories' => $categories,
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
