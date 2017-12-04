<?php

namespace AppBundle\Controller\Test;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Tests\StringableObject;

class TestController extends Controller
{
    /**
     * @Route("/query")
     */
    public function QuerytesterAction(Request $request,Connection $connection)
    {
        echo "<h1>Queries</h1>";
        echo "<p>get on row</p>";
        var_dump($connection->fetchArray("select password,salt,roles from user where username = ?", array("user")));
        echo "<br>";
        echo "<p>get onew valuew</p>";
        echo var_dump($connection->fetchColumn("select username from user where username = ?", array("maxi")));
        echo "<br>";
        echo "<p>get all</p>";
        echo var_dump($connection->fetchAll("Select * from user"));

//        $connection->insert("user",array("username" => "der","password" => "aaaa"));
//        $connection->update("user",array("username" => "adrian","password" => "sfäöasfljaskpfääas"),array("userId" => 4));
        return new Response('', Response::HTTP_OK);;
    }
}
