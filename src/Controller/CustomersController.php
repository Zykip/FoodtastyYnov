<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Entity\Reviews;
use App\Form\CustomersType;
use App\Repository\CustomersRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/customers")
 */
class CustomersController extends AbstractController
{
    /**
     * @Route("/", name="customers_index", methods={"GET", "POST"})
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod('POST')){
            if(!$this->isCsrfTokenValid('authenticate', $request->request->get('_csrf_token'))){
                return $this->json([
                    'status' => 'FAILED',
                    'message' => 'Invalid CSRF Token'
                ]);
            }

            //check username
            $dupUsername = $em->createQueryBuilder()
                ->from('App:Users', 'u')
                ->select('u.username')
                ->andWhere('u.id != :id')->setParameter('id', $this->getUser()->getId())
                ->andWhere('u.username = :username')->setParameter('username', $request->request->get('username'))
                ->setMaxResults(1)
                ->getQuery()->getOneOrNullResult();
            if($dupUsername !== null){
                return $this->json([
                    'status' => 'FAILED',
                    'message' => 'This username is already in use, choose a different one'
                ]);
            }

            $user = $this->getUser();
            if(trim($request->request->get('password')) != ''){
                $user->setPassword($encoder->encodePassword($user, $request->request->get('password')));
            }
            $em->persist($user);

            $customer = $this->getUser()->getCustomer();
            $customer->setFirstName($request->request->get('firstname'))
                ->setName($request->request->get('lastname'))
                ->setEmail($request->request->get('email'))
                ->setPostalAddress($request->request->get('address'));
            $em->persist($customer);

            $em->flush();

            return $this->json([
                'redirect' => $this->generateUrl('customers_index')
            ]);
        }

        return $this->render('customers/profile.html.twig', [
        ]);
    }


    /**
     * @Route("/orders", name="customers_orders")
     */
    public function orders(){
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('App:Orders')->findBy([
            'customer' => $this->getUser()->getCustomer()
        ]);

        return $this->render('customers/orders.html.twig', [
            'orders' => $orders
        ]);
    }
	
	/**
	 * @Route("/orders/view/{id}", name="customers_orders_view")
	 */
	public function view(Request $request, $id){
		$em = $this->getDoctrine()->getManager();
		$orders = $em->getRepository('App:Orders')->find($id);
		
		return $this->render('admin/view_order.html.twig', [
			'order' => $orders
		]);
	}

    /**
     * @Route("/reviews/{id}", name="customers_add_reviews")
     */
    public function reviews(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('App:Orders')->find($id);

        if($request->isMethod('POST')){
            //add a review
            foreach($request->request->get('dish_id') as $key => $dish_id){
                $dish = $em->getRepository('App:Dishes')->find($dish_id);
                $review = new Reviews();

                $review->setDish($dish)
                    ->setCreatedAt(Carbon::now())
                    ->setUser($this->getUser())
                    ->setDescription($request->request->get('description')[$dish_id])
                    ->setStars($request->request->get('rating')[$dish_id])
                    ->setRestaurant($dish->getRestaurant())
                ;
                $em->persist($review);
            }

            $em->flush();

            return $this->json([
                'redirect' => $this->generateUrl('customers_orders')
            ]);
        }

        return $this->render('customers/review.html.twig', [
            'order' => $order
        ]);
    }
}