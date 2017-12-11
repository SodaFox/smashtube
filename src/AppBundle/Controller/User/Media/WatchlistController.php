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
 * Class MediaController
 * @package AppBundle\Controller\User\Media
 * @Security("has_role('ROLE_USER')")
 */
class WatchlistController extends Controller
{
    /**
     * @Route("/user/media/watchlist")
     * @Method({"GET"})
     */
    public function getWatchlistAction(Request $request,Connection $con)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();;
        $result = $con->fetchAll("
        select wl.media_id as episode_id,m.title as episode_title, m.description as episode_description, 
        m.season, md.title as media_title, md.id as media_id from watch_list wl
        join media m on m.id = wl.media_id
        join media_description md on md.id = m.description_id
        where user_id = ?",
            array($user->getId()));

        return new JsonResponse($result);
    }
    /**
     * @Route("/user/media/watchlist")
     * @Method({"POST"})
     */
    public function postWatchlistAction(Request $request,Connection $connection)
    {
        $formData = $request->request->all();

        $result = $connection->insert("watch_list",array(
            "media_id" => $formData["episode_id"],
            "user_id" => $this->getUser()->getId()
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
    /**
     * @Route("/user/media/watchlist")
     * @Method({"DELETE"})
     */
    public function deleteWatchlistAction(Request $request,Connection $connection)
    {
        $episodeId = $request->query->get("episode_id");

        $result = $connection->executeQuery("delete from watch_list where media_id = ? and user_id = ?",
            array($episodeId,$this->getUser()->getId()));
        return new JsonResponse();
    }
}
