<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    private $eventDispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $totalOrders = $em->createQueryBuilder()
            ->from('App:Orders', 'o')
            ->select('COUNT(o)')
            ->getQuery()->getSingleScalarResult();

        $runningOrders = $em->createQueryBuilder()
            ->from('App:Orders', 'o')
            ->select('COUNT(o)')
            ->andWhere('o.isDelivered = 0')
            ->getQuery()->getSingleScalarResult();

        $revenue = $em->createQueryBuilder()
            ->from('App:OrdersDishes', 'od')
            ->join('od.dish', 'dish')
            ->join('od.order', 'o')
            ->select('COALESCE(SUM(od.quantity * 2.5), 0)')
            ->andWhere('o.isDelivered = 1')
            ->getQuery()->getSingleScalarResult();

        return $this->render('admin/index.html.twig', [
            'totalOrders' => $totalOrders,
            'runningOrders' => $runningOrders,
            'revenue' => $revenue
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request){
        $em = $this->getDoctrine()->getManager();

        if($this->getUser() != null){
            return $this->redirectToRoute('admin_index');
        }

        if($request->isMethod('POST')){
            if(!$this->isCsrfTokenValid('authenticate', $request->request->get('_csrf_token'))){
                throw new InvalidCsrfTokenException('Invalid CSRF Token');
            }
            //check for login
            $checkUser = $em->getRepository('App:Users')->findOneBy([
                'username' => $request->request->get('username'),
                'isActive' => 1
            ]);
            if($checkUser == null){
                return $this->json([
                    'status' => 'FAILED',
                    'message' => 'Invalid username'
                ]);
            }

            //match password
            if(!password_verify($request->request->get('password'), $checkUser->getPassword()) && in_array("ROLE_ADMIN", $checkUser->getRoles())){
                return $this->json([
                    'status' => 'FAILED',
                    'message' => 'User not found'
                ]);
            }

            //auto login
            //create new user password token
            $token = new UsernamePasswordToken($checkUser, $checkUser->getPassword(), "main", $checkUser->getRoles());

            //set token for security
            $this->get("security.token_storage")->setToken($token);

            // Fire the login event
            // Logging the user in above the way we do it doesn't do this automatically
            $event = new InteractiveLoginEvent($request, $token);

            //dispatch auto login event
            $this->eventDispatcher->dispatch($event);

            return $this->json([
                'redirect' => $this->generateUrl('admin_index'),
                'message' => 'Logged in successfully',
                'status' => 'OK'
            ]);
        }
        return $this->render('admin/login.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/orders", name="orders_index")
     */
    public function orders(){

        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('App:Orders')->findAll();

        return $this->render('admin/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/orders/view/{id}", name="orders_view")
     */
    public function view(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('App:Orders')->find($id);

        return $this->render('admin/view_order.html.twig', [
            'order' => $orders
        ]);
    }
}
