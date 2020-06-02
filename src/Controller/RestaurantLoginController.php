<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Restaurants;
use App\Entity\RestaurantsUsers;
use App\Entity\Users;
use App\Form\RestaurantsType;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class RestaurantLoginController extends AbstractController
{

    private $eventDispatcher;
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @Route("/restaurant/login", name="restaurant_login")
     */
    public function login(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser() != null){
            return $this->redirectToRoute('restaurants_profile');
        }

        $form = $this->createFormBuilder(null, [
            'attr' => [
                'class' => 'js-form'
            ]
        ])->add('username')
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Login'
            ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //check for login
            $checkUser = $em->getRepository('App:Users')->findOneBy([
                'username' => $form->get('username')->getData(),
                'isActive' => 1
            ]);
            if($checkUser == null){
                return $this->json([
                    'status' => 'FAILED',
                    'message' => 'Invalid username'
                ]);
            }

            //match password
            if(!password_verify($form->get('password')->getData(), $checkUser->getPassword()) && in_array("ROLE_RESTAURANT", $checkUser->getRoles())){
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
                'redirect' => $this->generateUrl('restaurants_profile'),
                'message' => 'Logged in successfully',
                'status' => 'OK'
            ]);
        }

        return $this->render('restaurant_login/login.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/restaurant/register", name="restaurant_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder){

        if($this->getUser() != null){
            return $this->redirectToRoute('restaurants_profile');
        }

        $em = $this->getDoctrine()->getManager();
        $restaurant = new Restaurants();
        $form = $this->createForm(RestaurantsType::class, $restaurant);
        $form
            ->add('username', null, [
                'mapped' => false,
                'attr' => [
                    'data-rule-remote' => $this->generateUrl('app_validate_email'),
                    'data-msg-remote' => 'This username is already registered with us, please use a different one'
                ]
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false
            ])
        ;
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = new Users();
            $user->setUsername($form->get('username')->getData())
                ->setPassword($encoder->encodePassword($user, $form->get('password')->getData()))
                ->setRoles(['ROLE_RESTAURANT'])
                ->setSalt(uniqid())
                ->setIsActive(true)
                ->setCreatedAt(Carbon::now());
            $em->persist($user);

            //upload media
            $photo = $form->get('mediaId')->getData();
            $photoName = uniqid('photo_').'.'.$photo->getClientOriginalExtension();
            $photo->move('upload/images', $photoName);
            $media = new Media();
            $media->setName($photoName);
            $em->persist($media);

            //persist restaurant
            $restaurant->setMedia($media)
                ->setIsActive(true)
            ;
            $em->persist($restaurant);

            //attach
            $rUser = new RestaurantsUsers();
            $rUser->setUser($user)
                ->setRestaurant($restaurant);
            $em->persist($rUser);

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
                'redirect' => $this->generateUrl('restaurants_profile'),
                'message' => 'Registered successfully',
                'status' => 'OK'
            ]);
        }

        return $this->render('restaurant_login/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}