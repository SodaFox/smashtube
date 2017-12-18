<?php

namespace AppBundle\Controller;

use AppBundle\Common\QueryBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Tests\StringableObject;
use Symfony\Component\HttpFoundation\JsonResponse;


class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request, Connection $connection)
    {
        $result = $connection->fetchAll("
        SELECT title, thumbnail, description, path FROM media UNION SELECT title, description, thumbnail, path FROM media_description
        ");

        return $this->render('default/index.html.twig',array(
            'medias' => $result
        ));
        //return $this->render('default/index.html.twig', [
        //    'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        //]);
    }

}
