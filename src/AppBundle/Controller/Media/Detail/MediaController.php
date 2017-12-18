<?php

namespace AppBundle\Controller\Media\Detail;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Choice;


///media/7/edit/episodes/{season}/{episode
class MediaController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/media/{mediaId}", requirements={"mediaId": "\d+"})
     */
    public function getMediaAction(Request $request,Connection $connection,$mediaId)
    {
        $result = $connection->fetchAssoc("
            select d.id,d.title, d.description,
            m.duration,m.thumbnail,m.path,m.title as episode_title, m.description as episode_description,
            m.season, m.episode_number, c.genre
            from media_description d
            left outer join media m on d.id = m.description_id
            left outer join media_category mc on mc.description_id = d.id
            left outer join category c on mc.category_id = c.id
            where d.id = ?;
        ",array($mediaId));

        return new JsonResponse($result);
    }
    /**
     * @Route("/media/{mediaId}/edit", requirements={"mediaId": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function putMediaAction(Request $request,Connection $connection,$mediaId)
    {
        $result = $connection->fetchAssoc("select d.id,d.title,d.description, sec_to_time(sum(time_to_sec(m.duration))) as duration_total, 
        max(m.season) as season_count from media_description d
        join media m on m.description_id = d.id
        where d.id = ?",array($mediaId));

        $categories = $this->_transformCategories($connection,$mediaId);
        $allCategories = $this->_transformCategories($connection,-1);

        $form = $this->createFormBuilder($result)
            ->add('id', NumberType::class)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('duration_total', TimeType::class,array('input' => "string","disabled" => true))
            ->add('season_count', NumberType::class,array("disabled" => true))
            ->add('genre',ChoiceType::class,
                    array(
                            "multiple" => true,
                            "choices" => $allCategories,
                            'data' => $categories
                        )
                )
            ->add('save', SubmitType::class, array('label' => 'Speichern'))
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
    /**
     * @Route("/media/{mediaId}/add", requirements={"mediaId": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function postEpisodeAction(Request $request,Connection $connection,$mediaId)
    {
        $data = array();
        $form = $this->createFormBuilder($data)
            ->add("episode_number", NumberType::class)
            ->add("duration", TimeType::class,array('input' => "string"))
            ->add("season", NumberType::class)
            ->add("title", TextType::class)
            ->add("description", TextareaType::class)
            ->add("file", FileType::class)
            ->add('save', SubmitType::class, array('label' => 'Speichern'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $file = $data["file"];

            if(in_array($file->guessExtension() ,$this->getParameter("media_types")) == false)
            {
                $form->addError(new FormError("Datentyp wird nicht unterstützt"));
                return $this->render('media/detail/get.html.twig', array(
                    'form' => $form->createView(),
                ));
            }

            $baseDir = $this->getParameter("media_directory");
            $filename = "episode_{$data["episode_number"]}." . $file->guessExtension();
            $dir = $baseDir . "{$mediaId}\\". $data["season"] . "\\";

            $file->move($dir,$filename);

            $filepath = $dir . "\/" . $filename;

            $connection->insert("media",array(
                "episode_number" => $data["episode_number"],
                "duration" => $data["duration"],
                "title" => $data["title"],
                "path" => $filepath,
                "season" => $data["season"],
                "description_id" => $mediaId,
                "description" => $data["description"]
            ));
        }

        return $this->render('media/detail/get.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    private  function _updateCategories(Connection $connection,$mediaId, $newCategories)
    {
        $currentcategories = $connection->fetchAll("select c.category_id from media_category c where c.description_id = ?"
        ,array($mediaId));

        $currentTransformend = array();
        $newTransformed = array();

        foreach ($currentcategories as $cat)
        {
            array_push($currentTransformend,$cat["category_id"]);
        }
        foreach ($newCategories as $cat)
        {
            array_push($newTransformed,$cat);
        }

        //get new data
        $insertArray = array_diff($newTransformed,$currentTransformend);
        //get data to delete
        $deleteArray = array_diff($currentTransformend,$newTransformed);

        if($insertArray != null)
        {
            $sql = array();
            //insert HERE
            foreach($insertArray as $item) {
                $sql[] = "({$item},{$mediaId})";
            }
            $connection->executeQuery("Insert into media_category (category_id,description_id) VALUES " .
            implode(",",$sql));
        }
        if($deleteArray != null)
        {
            $sql = array();
            //insert HERE
            foreach($deleteArray as $item)
            {
                $connection->delete("media_category",array(
                    "category_id" => $item,
                    "description_id" => $mediaId
                ));
            }
        }
    }
    private function  _transformCategories(Connection $connection, $mediaId)
    {
        //get Category array
        if($mediaId < 1)
        {
            $categories = $connection->fetchAll("
               select c.id,c.genre from category c
            ");
        }
        else
        {
            $categories = $connection->fetchAll("
                select c.id, c.genre from media m
                join media_category mc on mc.description_id = m.description_id
                join category c on mc.category_id = c.id
                WHERE   m.description_id = ?
            ",array($mediaId));
        }


        $categoriesTransformed = array();
        //transform category
        foreach ($categories as $val)
        {
            $categoriesTransformed[$val["genre"]] = $val["id"];
        }

        return $categoriesTransformed;
    }
}
