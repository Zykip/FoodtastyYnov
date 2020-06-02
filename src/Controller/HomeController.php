<?php

namespace App\Controller;

use App\Repository\RestaurantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RestaurantsRepository $restaurants)
    {
        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurants->findAll()
        ]);
    }
}
