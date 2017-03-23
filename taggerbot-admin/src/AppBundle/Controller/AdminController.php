<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
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
        return $this->render('doc.html.twig');
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

    public function uploadFileAction(){
        return new JsonResponse(array('success'=>true));
    }

    public function queryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare("select * from pg_stat_activity");
        $stmt->execute();

        return new JsonResponse($stmt->fetchAll());
    }
}