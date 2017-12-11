<?php

namespace AppBundle\Controller\Media;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MediaController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/media")
     */
    public function getMediaAction(Request $request,Connection $connection)
    {
        $result = $connection->fetchAll("
        select d.id,m.duration,d.title, d.description,max(m.season)as season_count,m.thumbnail  from media m
        join media_description d on d.id = m.description_id
        group by m.description_id
        ");

        return new JsonResponse($result);
//        return $this->render('media/getAll.html.twig',array(
//            'medias' => $result
//        ));
    }

    /**
     * @Method({"POST"})
     * @Route("/media")
     */
    public function postMediaAction(Request $request,Connection $connection)
    {
        $formData = $request->request->all();

        $result = $connection->insert("media_description",array(
            "title" => $formData["title"],
            "description" => $formData["description"]
        ));

        if($result > 0)
        {
            return new JsonResponse(true);
        }
        else
        {
            return new JsonResponse(false);
        }
    }
}
