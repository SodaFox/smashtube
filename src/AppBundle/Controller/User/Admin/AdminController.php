<?php

namespace AppBundle\Controller\User\Admin;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    /**
     * @Route("/user/admin")
     */
    public function getAdminAction(Request $request,Connection $con)
    {
        $admin = $con->fetchAssoc("Select * from user where user = ?",array(1));

        return $admin;
    }
}
