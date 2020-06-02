<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Media;
use App\Form\DishesType;
use App\Repository\DishesRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/restaurants/dishes")
 */
class DishesController extends AbstractController
{
    /**
     * @Route("/", name="dishes_index", methods={"GET"})
     */
    public function index(DishesRepository $dishesRepository): Response
    {
        return $this->render('dishes/index.html.twig', [
            'dishes' => $dishesRepository->findBy([
                'restaurant' => $this->getUser()->getRestaurant()
            ]),
        ]);
    }

    /**
     * @Route("/new", name="dishes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $dish = new Dishes();
        $form = $this->createForm(DishesType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //upload photo
            $photo = $form->get('mediaId')->getData();
            $photoName = uniqid('photo_').'.'.$photo->guessClientExtension();
            $photo->move('upload/images', $photoName);

            $media = new Media();
            $media->setName($photoName);
            $entityManager->persist($media);

            $dish->setMedia($media)
                ->setCreatedAt(Carbon::now())
                ->setRestaurant($this->getUser()->getRestaurant()->getRestaurant())
            ;
            $entityManager->persist($dish);
            $entityManager->flush();

            return $this->json([
                'redirect' => $this->generateUrl('dishes_index')
            ]);
        }

        return $this->render('dishes/new.html.twig', [
            'dish' => $dish,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="dishes_show", methods={"GET"})
     */
    public function show(Dishes $dish): Response
    {
        return $this->render('dishes/show.html.twig', [
            'dish' => $dish,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="dishes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Dishes $dish): Response
    {
        $form = $this->createForm(DishesType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //upload photo
            $photo = $form->get('mediaId')->getData();
            if($photo) {
                $photoName = uniqid('photo_') . '.' . $photo->guessClientExtension();
                $photo->move('upload/images', $photoName);

                $media = new Media();
                $media->setName($photoName);
                $entityManager->persist($media);

                $dish->setMedia($media)
                    ->setRestaurant($this->getUser()->getRestaurant()->getRestaurant());
            }

            $entityManager->persist($dish);
            $entityManager->flush();

            return $this->json([
                'redirect' => $this->generateUrl('dishes_index')
            ]);
        }

        return $this->render('dishes/edit.html.twig', [
            'dish' => $dish,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="dishes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Dishes $dish): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dish->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dish);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dishes_index');
    }
}
