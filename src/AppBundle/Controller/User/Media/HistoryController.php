<?php

namespace AppBundle\Controller\User\Media;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HistoryController
 * @package AppBundle\Controller\User\Media
 * @Security("has_role('ROLE_USER')")
 */
class HistoryController extends Controller
{
    /**
     * @Route("/user/media/history")
     * @Method({"GET"})
     */
    public function getHistoryAction(Request $request,Connection $con)
    {
        $episode = $request->query->get("episode_id");
        $user = $this->getUser();

        $result = $con->fetchAll("
            select t.id,t.timestamp, m.title,m.season,m.id as episode_id,m.duration,m.realtime,m.type
            from timestamp t 
            join media m on t.media_id = m.id
            where t.user_id = 1 and t.timestamp > m.duration",
            array(
                $user->getId()
            ));

        return new JsonResponse($result);
    }

//    /**
//     * @Route("/user/media/history")
//     * @Method({"DELETE"})
//     */
//    public function deleteHistoryAction(Request $request,Connection $con)
//    {
//        $episode = $request->get("episode_id");
//        $user = $this->getUser();
//
//        $result = $con->delete("timestamp",
//            array(
//                "user_id" => $user->getid(),
//                "media_id" => $episode
//            ));
//
//        return new Response();
//    }
}
