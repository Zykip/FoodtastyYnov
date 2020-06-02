<?php

namespace App\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
		/**
		 * @var $q QueryBuilder
		 */
    	$q = $em->createQueryBuilder();
    	$restaurants = $q
			->from('App:Restaurants', 'r')
			->select('r')
			->andWhere('r.address LIKE :location')->setParameter('location', '%'.$request->query->get('location').'%')
			->join('r.dishes', 'd', 'WITH', 'r.id = d.restaurantId')
			->andWhere('d.name LIKE :dish')->setParameter('dish', '%'.$request->query->get('item').'%')
			->getQuery()->getResult();
    	
        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurants
        ]);
    }
}
