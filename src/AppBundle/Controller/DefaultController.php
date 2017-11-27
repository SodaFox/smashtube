<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\DriverManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        echo "fuck it";
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/test")
     */
    public function testAction(Request $request)
    {
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'dbname' => 'smashtube',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $sql = "SELECT * FROM user u where u.username = :name";
        $query= $conn->prepare($sql);
        $query->bindValue("name", "maxi");
        $query->execute();
        var_dump($query->fetch());


        //TODO ADD COMMON CLASS HERE

        echo "in test";
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/user")
     */
    public function securedAction(Request $request)
    {
        echo "in user";
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
