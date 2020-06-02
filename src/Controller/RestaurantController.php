<?php

namespace App\Controller;

use App\Entity\Restaurants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    /**
     * @Route("/restaurant/view/{id}", name="restaurant")
     */
    public function index(Request $request, Restaurants $restaurant)
    {
        return $this->render('restaurant/index.html.twig', [
            'restaurant' => $restaurant
        ]);
    }
}
