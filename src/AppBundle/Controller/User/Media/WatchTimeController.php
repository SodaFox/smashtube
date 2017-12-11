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
 * Class WatchTimeController
 * @package AppBundle\Controller\User\Media
 * @Security("has_role('ROLE_USER')")
 */
class WatchTimeController extends Controller
{
    /**
     * @Route("/user/media/watchtime")
     * @Method({"GET"})
     */
    public function getWatchTimeAction(Request $request,Connection $con)
    {
        $episode = $request->query->get("episode_id");
        $user = $this->getUser();

        $result = $con->fetchAssoc("select t.id as watch_id,t.timestamp as time from timestamp t where t.user_id = ? and t.media_id = ?",
            array($user->getId(),$episode));
//        var_dump($result);
        return new JsonResponse($result);
    }
    /**
     * @Route("/user/media/watchtime")
     * @Method({"POST"})
     */
    public function postWatchTimeAction(Request $request,Connection $connection)
    {
        $episode = $request->get("episode_id");
        $time = $request->get("time");
        $user = $this->getUser();

        $connection->insert("timestamp",
            array(
                "media_id" => $episode,
                "timestamp" => $time,
                "user_id" => $user->getId()
            ));
//        var_dump($result);
        return new JsonResponse();
    }
    /**
     * @Route("/user/media/watchtime")
     * @Method({"PUT"})
     */
    public function putWatchTimeAction(Request $request,Connection $connection)
    {
        $episode = $request->get("episode_id");
        $time = $request->get("time");
        $user = $this->getUser();

        $connection->update("timestamp",
            array(
                "timestamp" => $time
            ),array(
                "user_id" => $user->getId(),
                "media_id" => $episode
                ));
        return new Response();
    }

}
