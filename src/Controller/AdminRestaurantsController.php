<?php

namespace App\Controller;

use App\Entity\Restaurants;
use App\Form\RestaurantsType;
use App\Repository\RestaurantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/restaurants", name="admin_")
 */
class AdminRestaurantsController extends AbstractController
{
    /**
     * @Route("/", name="restaurants_index", methods={"GET"})
     */
    public function index(RestaurantsRepository $restaurantsRepository): Response
    {
        return $this->render('admin/restaurants/index.html.twig', [
            'restaurants' => $restaurantsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="restaurants_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $restaurant = new Restaurants();
        $form = $this->createForm(RestaurantsType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('admin_restaurants_index');
        }

        return $this->render('admin/restaurants/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restaurants_show", methods={"GET"})
     */
    public function show(Restaurants $restaurant): Response
    {
        return $this->render('admin/restaurants/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="restaurants_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Restaurants $restaurant): Response
    {
        $form = $this->createForm(RestaurantsType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_restaurants_index');
        }

        return $this->render('admin/restaurants/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restaurants_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Restaurants $restaurant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_restaurants_index');
    }
}
