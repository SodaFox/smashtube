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
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


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
    public function resetAction(Request $request,AuthenticationUtils $authUtils, UserPasswordEncoderInterface $passwordEncoder, Connection $connection, EncoderFactory $encoderFactory)
    {
        if(isset($_POST['_username'])&& isset($_POST['form']['question']) && isset($_POST['answer']) && isset($_POST['new-password'])&& isset($_POST['new-password-repeat']))
        {
            $result = $connection->fetchAll("select answer, username, question_id from user where username ='$_POST[_username]'");
            if ($_POST['form']['question'] == $result[0]['question_id'])
            {
                $user = new User();
                $answer = $passwordEncoder->encodePassword($user, $_POST['answer']);
                $encoder = $encoderFactory->getEncoder($user);
                $res = $encoder->isPasswordValid($result[0]['answer'],$_POST['answer'],$user->getSalt());

                if($res)
                {
                    if ($_POST['new-password'] == $_POST['new-password-repeat'])
                    {
                        $password = $passwordEncoder->encodePassword($user, $_POST['new-password']);
                        $connection->executeQuery("UPDATE user SET password = '$password' WHERE username = '$_POST[_username]'");
                    }
                }
            }
        }

        $result = $connection->fetchALl("select * from question");
        $questionsTransformed = array();
        foreach($result as $results){
            $questionsTransformed[$results["text"]] = $results["id"];
        }


        $form = $this->createFormBuilder(null)
            ->add("question", ChoiceType::class, array(
                'choices' => $questionsTransformed
            ))
            ->getForm();

        $form->handleRequest($request);

        return $this->render(
            'security/reset_password.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/user/security/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, Connection $connection)
    {
        if(isset($_POST['form'])){
            $user = new User();
            $password = $passwordEncoder->encodePassword($user ,$_POST['form']['password']['first']);
            $answer = $passwordEncoder->encodePassword($user ,$_POST['form']['answer']);

            $connection->insert("user",array(
                "username" => $_POST['form']['username'],
                "birthday" => $_POST['form']['birthday'],
                "password" => $password,
                "answer" => $answer,
                "question_id" => $_POST['form']['question']
            ));
            return new JsonResponse();
        }
        $result = $connection->fetchALl("select * from question");

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

        /*if ($form->isSubmitted() && $form->isValid())
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

        }*/



        return $this->render(
            'registration/detail/registration.html.twig',
            array('form' => $form->createView())
        );

    }
}
