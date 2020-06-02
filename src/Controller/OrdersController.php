<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Entity\Orders;
use App\Entity\OrdersDishes;
use App\Entity\Users;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class OrdersController extends AbstractController
{
    private $eventDispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @Route("/orders/add", name="orders_add", methods={"POST"})
     */
    public function add(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer){
        $em = $this->getDoctrine()->getManager();

        if(!$this->isCsrfTokenValid('place_order', $request->request->get('_csrf_token'))){
            return $this->json([
                'status' => 'FAILED',
                'message' => 'Invalid CSRF Token'
            ]);
        }

        //create or get customer
        $customer = null;
        if($this->getUser() != null && $this->isGranted('ROLE_CUSTOMER')){
            $customer = $this->getUser()->getCustomer();
        }

        if($customer == null){
            //create or login using credentials
            //check if exists
            $hasUser = $em->getRepository('App:Users')->findOneBy([
                'username' => $request->request->get('username'),
                'isActive' => 1,
                'roles' => ['ROLE_CUSTOMER']
            ]);
            if($hasUser != null){
                $customer = $hasUser->getCustomer();
                $customer->setPostalAddress($request->request->get('address'))
                    ->setFirstName($request->request->get('firstname'))
                    ->setName($request->request->get('name'))
                    ->setEmail($request->request->get('email'))
                ;
            }

            if($customer == null){
                //create new customer
                $user = new Users();
                $user->setUsername($request->request->get('username'))
                    ->setPassword($encoder->encodePassword($user, $request->request->get('password')))
                    ->setRoles(['ROLE_CUSTOMER'])
                    ->setSalt(uniqid())
                    ->setIsActive(true)
                    ->setCreatedAt(Carbon::now());
                $em->persist($user);

                //add customer
                $customer = new Customers();
                $customer->setUser($user)
                    ->setName($request->request->get('lastname'))
                    ->setFirstName($request->request->get('firstname'))
                    ->setEmail($request->request->get('email'))
                    ->setPostalAddress($request->request->get('address'))
                    ->setBalance(0);
                $em->persist($customer);
            }
        }

        //place order
        $query = $em->createQueryBuilder()
            ->from('App:CartDishes', 'cd')
            ->select('cd')
            ->andWhere('cd.ipAddress = :ip')->setParameter('ip', $request->getClientIp())
            ->andWhere('cd.userAgent = :userAgent')->setParameter('userAgent', $request->server->get('HTTP_USER_AGENT'))
        ;
        if($this->getUser() != null){
            $query->andWhere('cd.user = :user')->setParameter('user', $this->getUser());
        }

        $cartItems = $query->getQuery()->getResult();

        if(!empty($cartItems)) {
            $order = new Orders();
            $order->setCreatedAt(Carbon::now())
                ->setRestaurant($cartItems[0]->getRestaurant())
                ->setCustomer($customer)
                ->setCustomerAddress($customer->getPostalAddress())
                ->setCustomerPhone('')
                ->setDeliveryCost(0)
                ->setIsDelivered(false)
                ->setOrderNumber(1)
                ->setDeliverAt(Carbon::now()->addHour())
            ;
            $em->persist($order);

            $cost = 0;
            foreach ($cartItems as $item) {
                $orderItem = new OrdersDishes();
                $orderItem->setQuantity($item->getQuantity())
                    ->setDish($item->getDish())
                    ->setOrder($order)
                    ->setPrice($item->getDish()->getPrice())
                ;
                $em->persist($orderItem);

                $em->remove($item);

                $cost += $item->getQuantity() * 2.5;
            }

            $order->setDeliveryCost($cost);
            $em->persist($order);
        }

        $em->flush();

        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('idrissmous1@gmail.com')
            ->setTo($cartItems[0]->getRestaurant()->getEmail())
            ->setBody(
                $this->renderView(
                    'orders/email.html.twig',
                    ['order' => $order]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        //auto login if not logged in
        if($this->getUser() == null) {
            $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $this->eventDispatcher->dispatch($event);
        }

        return $this->json([
            'status' => 'OK',
            'message' => '',
            'redirect' => $this->generateUrl('customers_index')
        ]);
    }
}
