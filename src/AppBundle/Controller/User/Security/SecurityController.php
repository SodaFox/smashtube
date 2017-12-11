<?php

namespace AppBundle\Controller\User\Security;

use Doctrine\DBAL\Connection;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/user/security/login", name="login")
     */
    public function loginAction(Request $request,AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/user/security/logout")
     */
    public function logoutAction(Request $request,AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
    }

    /**
     * @Route("/user/security/reset")
     */
    public function resetAction(Request $request,AuthenticationUtils $authUtils)
    {
        return $this->render("security/reset_password.html.twig");
    }

    /**
     * @Route("/user/security/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, Connection $connection)
    {
        $result = $connection->fetchall("select * from question");

        $questionids=array();
        $questions=array();
        $questionsTransformed = array();
        foreach($result as $results){
            $questionsTransformed[$results["text"]] = $results["id"];
        }

        
        $form = $this->createFormBuilder($result)
            ->add("username", TextType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add("birthday", DateType::class, array('widget' => 'single_text',
                'input' => 'string'
                ))
            ->add("question", ChoiceType::class, array(
                //Danke Maxi
                'choices' => $questionsTransformed
            ))
            ->add("answer", TextType::class)
            ->add("Registrieren", SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $user = new User();
            
            $password = $passwordEncoder->encodePassword($user ,$data['password']);

            $answer = $passwordEncoder->encodePassword($user ,$data['answer']);

            $connection->insert("user",array(
                "username" => $data['username'],
                "birthday" => $data['birthday'],
                "password" => $password,
                "answer" => $answer,
                "question_id" => $data['question']
            ));
        }


        return $this->render(
            'registration/detail/registration.html.twig',
            array('form' => $form->createView())
        );

    }
}
