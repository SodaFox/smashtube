<?php
namespace AppBundle\Controller\Media\Detail\Season;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Choice;


class SeasonController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/media/{mediaId}/season", requirements={"mediaId": "\d+"})
     */
    public function getSeasonAction(Request $request,Connection $connection,$mediaId)
    {
        $seasons = $connection->fetchAll("select season from media where description_id = ? group by season", array($mediaId));

        $seasonArray = array();
        foreach ($seasons as $season)
        {
            $help = $season["season"];

            $episodes = $connection->fetchAll("
              select m.title, m.description,m.realtime,m.season,m.episode_number from media m
              where m.description_id = ? and season = ?
            ",array($mediaId,$help));

            $seasonArray[$help] = $episodes;
        }

        return new JsonResponse($seasonArray);
    }

}
