<?php

namespace AppBundle\Controller\Media;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MediaController extends Controller
{
    /**
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
}
