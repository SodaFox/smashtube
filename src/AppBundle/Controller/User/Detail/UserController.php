<?php

namespace AppBundle\Controller\User\Detail;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package AppBundle\Controller\User
 * @Security("has_role('ROLE_USER')")
 */
class UserController extends Controller
{
    /**
 * @Route("/user/{userId}", requirements={"mediaId": "\d+"})
 * @Method({"GET"})
 */
    public function getUserAction(Request $request,Connection $con,$userId)
    {
        $user = $this->getUser();
        $userData = $con->fetchAssoc("Select * from user where id = ?",array(
            $userId
        ));

        $form = $this->createFormBuilder($userData)
            ->add('user_id', NumberType::class)
            ->add('username', TextType::class)
            ->add('birthday', DateType::class,array('format' => 'yyyy-MM-dd','input' => "string"))
            ->add('is_admin', TextType::class)
            ->add('design_id',NumberType::class)
            ->add('save', SubmitType::class, array('label' => 'Speichern'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $con->update("user",array(
                'username' => $data["username"],
                "birthday" => $data["birthday"],
                "design_id" => $data["design_id"]
            ),array("user_id" => $data["user_id"]));

//            var_dump($con->errorInfo());
        }

        return $this->render('user/get.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}