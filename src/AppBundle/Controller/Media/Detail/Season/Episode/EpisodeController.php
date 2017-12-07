<?php

namespace AppBundle\Controller\Media\Detail\Season\Episode;

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
use Symfony\Component\Validator\Constraints\Time;

class EpisodeController extends Controller
{
    /**
     * @Route("/media/{mediaId}/season/{seasonId}", requirements={"mediaId": "\d+","seasonId": "\d+"})
     */
    public function getEpisodesAction(Request $request,Connection $connection,$mediaId,$seasonId)
    {
        $result = $connection->fetchAll("
          select m.title, m.description,m.realtime,m.season,m.episode_number from media m
          where m.description_id = ? and m.season = ?
        ",array($mediaId,$seasonId));

        return new JsonResponse($result);
    }

    /**
     * @Route("/media/{mediaId}/season/{seasonId}/{episodeId}", requirements={"mediaId": "\d+", "seasonId" : "\d+","episodeId" : "\d+"})
     */
    public function getEpisodeAction(Request $request,Connection $connection,$mediaId,$seasonId,$episodeId)
    {
        $result = $connection->fetchAll("
        select m.* from media m
          where m.description_id = ? and m.season = ? and m.episode_number = ?",
            array($mediaId,$seasonId,$episodeId));

        return new JsonResponse($result);
    }

    /**
     * @Route("/media/{mediaId}/season/{seasonId}/{episodeId}/edit", requirements={"mediaId": "\d+", "seasonId" : "\d+","episodeId" : "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function putEpisodeAction(Request $request,Connection $connection,$mediaId,$seasonId,$episodeId)
    {
        $result = $connection->fetchAssoc("select m.* from media m
          where m.description_id = ? and m.season = ? and m.episode_number = ?",
            array($mediaId,$seasonId,$episodeId));

        $form = $this->createFormBuilder($result)
            ->add('id', NumberType::class,array("disabled" => true))
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('thumbnail', TextType::class)
            ->add("path",TextType::class,array('disabled' => true))
            ->add('duration', TimeType::class,array('input' => "string"))
            ->add('realtime', TimeType::class,array('input' => "string","disabled" => true))
            ->add('season', NumberType::class,array("disabled" => true))
            ->add('episode_number', NumberType::class)
            ->add('save', SubmitType::class, array('label' => 'Speichern'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $connection->update("media",array(
                'title' => $data["title"],
                "description" => $data["description"],
                "thumbnail" => $data["thumbnail"],
                "path" => $data["path"],
                "duration" => $data["duration"],
                "episode_number" => $data["episode_number"]
            ),array("id" => $data["id"]));

        }

        return $this->render('media/detail/get.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
