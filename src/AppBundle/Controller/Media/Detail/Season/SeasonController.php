<?php
namespace AppBundle\Controller\Media\Detail\Season;

use Doctrine\DBAL\Connection;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Choice;


class SeasonController extends Controller
{
    /**
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

    /**
     * @Route("/media/{mediaId}/season/{seasonId}/add", requirements={"mediaId": "\d+", "seasonId" : "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function postEpisodeAction(Request $request,Connection $connection,$mediaId,$seasonId,$episodeId)
    {
        $result = $connection->fetchAssoc("select m.* from media m
          where m.description_id = ? and m.season = ? and m.episode_number = ?",
            array($mediaId,$seasonId,$episodeId));

        $data = array();
        $form = $this->createFormBuilder($data)
            ->add("file", FileType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $connection->update("media_description",array(
                'title' => $data["title"],
                "description" => $data["description"]
            ),array("id" => $data["id"]));

            //category here
            $this->_updateCategories($connection,$mediaId,$data["genre"]);
        }

        return $this->render('media/detail/get.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
