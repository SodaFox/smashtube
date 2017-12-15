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
        if(isset($_POST['_first_name'])){
            foreach($_POST as $key => $value){
                if(empty($value)){
                    return new JsonResponse("Formular nicht vollständig ausgefüllt", 418);
                }
            };
            
            $query = $con->insert("user_contact",array(
                "first_name" => $_POST['_first_name'],
                "last_name" => $_POST['_last_name'],
                "e_mail" => $_POST['_email'],
                "request" => $_POST['_smashtube-contact-input']
            ));

            if($query)
            {
                return new JsonResponse("Erfolgreich in Datenbank eingetragen", 200);
            }
            else
            {
                return new JsonResponse("Datensatz konnte nicht eingetragen werden", 418);
            }
        }
    }
}