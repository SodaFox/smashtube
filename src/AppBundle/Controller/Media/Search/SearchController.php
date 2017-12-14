<?php

namespace AppBundle\Controller\Media\Search;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;

class SearchController extends Controller
{
    /**
     * @Route("/search")
     */
    public function searchAction(Request $request, Connection $connection)
    {
        $searchString = $request->get('SearchString');

        $searchString ="%".$searchString."%";

        $searchString = $connection->quote($searchString);

        echo $searchString;

        $results = $connection->fetchAll("
select m.title as episode_title, m.id as episode_id, m.season, m.description_id as media_id, m.episode_number, md.title 
from media m 
INNER JOIN media_description md ON m.description_id = md.id 
INNER JOIN media_category mc ON md.id = mc.description_id
INNER JOIN category c ON mc.category_id = c.id 
where m.title like ".$searchString." or md.title like ".$searchString." or c.genre like ".$searchString." ");


       var_dump($results);

        return new JsonResponse($results);

    }



}