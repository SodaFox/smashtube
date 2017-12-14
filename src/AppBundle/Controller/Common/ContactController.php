<?php

namespace AppBundle\Controller\Common;

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
 * Class ContactController
 */
class ContactController extends Controller
{
    /**
 * @Route("/contact")
 * @Method({"GET"})
 */
    public function getContactAction(Request $request,Connection $con)
    {
        return $this->render('user/contact.html.twig');
    }

    /**
     * @Route("/contact")
     * @Method({"POST"})
     */
    public function postSendMail(Request $request,Connection $con)
    {   
        return http_response_code(200);
        foreach($_POST as $key => $value){
            if(empty($value)){
                return http_response_code(418);
            }
        };
        $db_con = mysqli("localhost","root", "","SmashTube");
        $query = $db_con->query("INSERT INTO user_contact(first_name, last_name, e_mail, request)
                                 VALUES(\'".$_POST['_first_name']."\', \'".$_POST['_last_name']."\', \'".$_POST['_email']."\', \'".$_POST['_smashtube-contact-input'].")");
        //if($query)
        {
            return http_response_code(200);
        }
    }
}