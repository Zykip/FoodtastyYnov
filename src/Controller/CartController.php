<?php

namespace App\Controller;

use App\Entity\CartDishes;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $firstname = '';
        $lastname = '';
        $username = '';
        $email = '';
        $address = '';
        if($this->getUser() != null && $this->isGranted('ROLE_CUSTOMER')){
            $firstname = $this->getUser()->getCustomer()->getFirstName();
            $lastname = $this->getUser()->getCustomer()->getName();
            $username = $this->getUser()->getUsername();
            $email = $this->getUser()->getCustomer()->getEmail();
            $address = $this->getUser()->getCustomer()->getPostalAddress();
        }

        if($request->isXmlHttpRequest()){
            return $this->json([
                'count' => $this->getCartCount($em, $request)
            ]);
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $this->getCart($em, $request),
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => $username,
            'email' => $email,
            'address' => $address
        ]);
    }

    /**
     * @Route("/cart/add", name="cart_add", methods={"POST"})
     */
    public function add(Request $request){
        $em = $this->getDoctrine()->getManager();

        if(!$this->isCsrfTokenValid('cart_add', $request->request->get('_csrf_token'))){
            return $this->json([
                'status' => 'FAILED',
                'message' => 'Invalid Request'
            ]);
        }

        /**
         * @var $query QueryBuilder
         * check if previous cart item related to current restaurant or not
         */
        $query = $em->createQueryBuilder();
        $query->from('App:CartDishes', 'cd')
            ->select('cd')
            ->setMaxResults(1)
            ->andWhere('cd.ipAddress = :ip')->setParameter('ip', $request->getClientIp())
            ->andWhere('cd.userAgent = :userAgent')->setParameter('userAgent', $request->server->get('HTTP_USER_AGENT'))
        ;
        if($this->getUser() != null){
            $query->andWhere('cd.user = :user')->setParameter('user', $this->getUser());
        }
        $lastItem = $query->getQuery()->getOneOrNullResult();

        if($lastItem != null && $lastItem->getRestaurantId() == $request->request->get('restaurant_id')){
            //check if dish already exists
            $dishExists = $this->hasDish($request->request->get('dish_id'), $em, $request);
            if($dishExists != null){
                //update quantity by 1
                $dishExists->setQuantity($dishExists->getQuantity() + 1)
                    ->setUpdatedAt(Carbon::now())
                ;
                $em->persist($dishExists);
            }else{
                $this->addNewDish($em, $request);
            }

            $em->flush();

            return $this->json([
                'status' => 'OK',
                'message' => 'Item Added',
                'data' => [
                    'cartCount' => $this->getCartCount($em, $request)
                ],
                'cb' => 'onAfterAddToCart'
            ]);
        }elseif($lastItem != null && $lastItem->getRestaurantId() != $request->request->get('restaurant_id')){
            return $this->json([
                'status' => 'FAILED',
                'message' => 'You can only order from 1 restaurant at a time'
            ]);
        }else{
            //add new dish
            $this->addNewDish($em, $request);

            return $this->json([
                'status' => 'OK',
                'message' => 'Item Added',
                'data' => [
                    'cartCount' => $this->getCartCount($em, $request)
                ],
                'cb' => 'onAfterAddToCart'
            ]);
        }
    }

    /**
     * @Route("/cart/remove", name="cart_remove")
     */
    public function remove(Request $request){
        $em = $this->getDoctrine()->getManager();

        $row = $em->getRepository('App:CartDishes')->find($request->request->get('id'));
        $em->remove($row);
        $em->flush();

        return $this->json([
            'status' => 'OK',
            'message' => ''
        ]);
    }

    private function hasDish($dishId, $em, Request $request): ?CartDishes
    {
        $conditions = [];
        $conditions['ipAddress'] = $request->getClientIp();
        $conditions['userAgent'] = $request->server->get('HTTP_USER_AGENT');
        $conditions['dishId'] = $dishId;
        if($this->getUser() != null){
            $conditions['user'] = $this->getUser();
        }

        $dish = $em->getRepository('App:CartDishes')->findOneBy($conditions);
        return $dish;
    }

    private function addNewDish($em, Request $request){
        $dish = new CartDishes();
        $dish->setRestaurant($em->getRepository('App:Restaurants')->find($request->request->get('restaurant_id')))
            ->setCreatedAt(Carbon::now())
            ->setUser($this->getUser())
            ->setDish($em->getRepository('App:Dishes')->find($request->request->get('dish_id')))
            ->setIpAddress($request->getClientIp())
            ->setQuantity(1)
            ->setUserAgent($request->server->get('HTTP_USER_AGENT'));
        $em->persist($dish);
        $em->flush();
    }

    public function getCartCount($em, $request){
        $total = $em->createQueryBuilder()
            ->from('App:CartDishes', 'cd')
            ->select('COUNT(cd)')
            ->andWhere('cd.ipAddress = :ip')->setParameter('ip', $request->getClientIp())
            ->andWhere('cd.userAgent = :userAgent')->setParameter('userAgent', $request->server->get('HTTP_USER_AGENT'))
        ;
        if($this->getUser() != null){
            $total->andWhere('cd.user = :user')->setParameter('user', $this->getUser());
        }

        return $total->getQuery()->getSingleScalarResult();
    }

    public function getCart($em, $request){
        $total = $em->createQueryBuilder()
            ->from('App:CartDishes', 'cd')
            ->select('cd')
            ->andWhere('cd.ipAddress = :ip')->setParameter('ip', $request->getClientIp())
            ->andWhere('cd.userAgent = :userAgent')->setParameter('userAgent', $request->server->get('HTTP_USER_AGENT'))
        ;
        if($this->getUser() != null){
            $total->andWhere('cd.user = :user')->setParameter('user', $this->getUser());
        }

        return $total->getQuery()->getResult();
    }
}
