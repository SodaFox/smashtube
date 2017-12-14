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

    /**
     * @Method({"GET"})
     * @Route("/media/add")
     */
    public function addMediaAction(Request $request,Connection $connection)
    {
//        $result = $connection->fetchAssoc("select d.id,d.title,d.description, sec_to_time(sum(time_to_sec(m.duration))) as duration_total,
//        max(m.season) as season_count from media_description d
//        join media m on m.description_id = d.id
//        where d.id = ?",array($mediaId));
        $allCategories = $this->_transformCategories($connection,-1);

        $form = $this->createFormBuilder(null)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
//            ->add('duration_total', TimeType::class,array('input' => "string","disabled" => true))
//            ->add('season_count', NumberType::class,array("disabled" => true))
            ->add('genre',ChoiceType::class,
                array(
                    "multiple" => true,
                    "choices" => $allCategories
                )
            )
            ->add('save', SubmitType::class, array('label' => 'Speichern'))
            ->getForm();

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $id = $connection->insert("media_description",
                [
                    'title' => $data["title"],
                    "description" => $data["description"]
                ]);

            die($id);
            //category here
//            $this->_updateCategories($connection,$mediaId,$data["genre"]);
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
