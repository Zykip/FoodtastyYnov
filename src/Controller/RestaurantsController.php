<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Restaurants;
use App\Entity\RestaurantsUsers;
use App\Entity\Users;
use App\Form\RestaurantsType;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class RestaurantsController extends AbstractController
{
    /**
     * @Route("/restaurants", name="restaurants_profile")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $restaurant = $this->getUser()->getRestaurant()->getRestaurant();
        $form = $this->createForm(RestaurantsType::class, $restaurant);
        $form
            ->remove('mediaId')
            ->add('mediaId', FileType::class, [
                'label' => 'Photo',
                'required' => false,
                'mapped' => false
            ])
            ->add('username', null, [
                'mapped' => false,
                'attr' => [
                    'data-rule-remote' => $this->generateUrl('app_validate_email'),
                    'data-msg-remote' => 'This email is already registered with us, please use a different one'
                ],
                'disabled' => true,
                'data' => $this->getUser()->getUsername()
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->remove('submit')
            ->add('submit', SubmitType::class, [
                'label' => 'Update'
            ])
        ;
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            if($form)
            $user->setUsername($form->get('username')->getData())
                ->setIsActive(true)
                ->setCreatedAt(Carbon::now());
            $em->persist($user);

            //upload media
            $photo = $form->get('mediaId')->getData();
            if($photo) {
                $photoName = uniqid('photo_') . '.' . $photo->getClientOriginalExtension();
                $photo->move('upload/images', $photoName);
                $media = new Media();
                $media->setName($photoName);
                $em->persist($media);

                //persist restaurant
                $restaurant->setMedia($media)
                    ->setIsActive(true);
            }
            $em->persist($restaurant);

            $em->flush();

            //redirect to restaurants secure portal
            return $this->json([
                'redirect' => $this->generateUrl('restaurants_profile'),
                'message' => 'Registered successfully',
                'status' => 'OK'
            ]);
        }
        return $this->render('restaurants/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/restaurants/orders", name="restaurants_orders")
     */
    public function orders(){
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('App:Orders')->findBy([
            'restaurantId' => $this->getUser()->getRestaurant()->getRestaurantId()
        ]);

        return $this->render('restaurants/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/restaurants/orders/view/{id}", name="restaurants_orders_view")
     */
    public function view(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('App:Orders')->find($id);

        return $this->render('admin/view_order.html.twig', [
            'order' => $orders
        ]);
    }

    /**
     * @Route("/restaurants/orders/complete", name="restaurants_orders_complete", methods={"POST"})
     */
    public function complete(Request $request){
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('App:Orders')->find($request->request->get('order_id'));
        if($order == null){
            throw new NotFoundHttpException('Order not found');
        }

        $order->setIsDelivered(true);
        $em->persist($order);
        $em->flush();

        return $this->json([
            'redirect' => $this->generateUrl('restaurants_orders')
        ]);
    }
}
