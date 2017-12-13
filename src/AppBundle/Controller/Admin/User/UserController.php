<?php

namespace AppBundle\Controller\Admin\User;

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
 * Class UserController
 * @package AppBundle\Controller\User
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends Controller
{
    /**
 * @Route("/admin/users")
 * @Method({"GET"})
 */
    public function getUsersAction(Request $request,Connection $con)
    {
        $user = $this->getUser();
        $userData = $con->fetchAll("Select * from user");

        return new JsonResponse($userData);
    }
}
