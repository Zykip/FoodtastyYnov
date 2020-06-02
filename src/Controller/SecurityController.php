<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Entity\Media;
use App\Entity\Restaurants;
use App\Entity\RestaurantsUsers;
use App\Entity\Users;
use App\Form\RestaurantsType;
use Carbon\Carbon;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityController extends AbstractController
{


    private $eventDispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('customers_index');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_signup")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder){
        if($this->getUser() != null){
            return $this->redirectToRoute('customers_index');
        }

        $em = $this->getDoctrine()->getManager();

        if($request->isMethod('POST')){
            if(!$this->isCsrfTokenValid('authenticate', $request->request->get('_csrf_token'))){
                throw new InvalidCsrfTokenException('Invalid CSRF Token');
            }

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

            $em->flush();

            //auto login
            //create new user password token
            $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());

            //set token for security
            $this->get("security.token_storage")->setToken($token);

            // Fire the login event
            // Logging the user in above the way we do it doesn't do this automatically
            $event = new InteractiveLoginEvent($request, $token);

            //dispatch auto login event
            $this->eventDispatcher->dispatch($event);

            //redirect to restaurants secure portal
            return $this->json([
                'redirect' => $this->generateUrl('customers_index'),
                'message' => 'Registered successfully',
                'status' => 'OK'
            ]);
        }

        return $this->render('security/register.html.twig', [
            'error' => ''
        ]);
    }

    /**
     * @Route("/security/validate-email", name="app_validate_email")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function validateEmail(Request $request){
        $em = $this->getDoctrine()->getManager();
        $username = $request->query->get('username');
        if(is_null($username)){
            if($request->query->has('customers')){
                $username = $request->query->get('customers')['username'];
            }
        }

        /**
         * @var $query QueryBuilder
         */
        $query = $em->createQueryBuilder();
        if($request->query->has('id')){
            $query->andWhere('u.id != :id')->setParameter('id', $request->query->get('id'));
        }

        $q = $query
            ->select('u')
            ->from(Users::class, 'u')
            ->andWhere('u.isActive = 1')
            ->andWhere('u.username = :username')->setParameter('username', $username)
            ->getQuery();

        return $this->json(count($q->getArrayResult()) === 0);
    }
}
