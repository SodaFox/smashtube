<?php

namespace AppBundle\Controller\User;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/user")
     * @Method({"GET"})
     */
    public function getUserAction(Request $request,Connection $con)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $userData = $con->fetchAssoc("Select * from user where user.user_id = ?",array(
            $user->getId()
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

        return $this->render('get.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
