<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Entity\Users;
use App\Form\CustomersType;
use App\Repository\CustomersRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/customers", name="admin_")
 */
class AdminCustomersController extends AbstractController
{
    /**
     * @Route("/", name="customers_index", methods={"GET"})
     */
    public function index(CustomersRepository $customersRepository): Response
    {
        return $this->render('customers/index.html.twig', [
            'customers' => $customersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="customers_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $customer = new Customers();
        $form = $this->createForm(CustomersType::class, $customer);
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

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = new Users();
            $user->setUsername($form->get('username')->getData())
                ->setPassword($encoder->encodePassword($user, $form->get('password')->getData()))
                ->setRoles(['ROLE_CUSTOMER'])
                ->setSalt(uniqid())
                ->setIsActive(true)
                ->setCreatedAt(Carbon::now());
            $entityManager->persist($user);

            $customer->setUser($user);
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirectToRoute('admin_customers_index');
        }

        return $this->render('customers/new.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customers_show", methods={"GET"})
     */
    public function show(Customers $customer): Response
    {
        return $this->render('customers/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="customers_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Customers $customer, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(CustomersType::class, $customer);

        $form
            ->add('username', null, [
                'mapped' => false,
                'attr' => [
                    'data-rule-remote' => $this->generateUrl('app_validate_email').'?id='.$customer->getId(),
                    'data-msg-remote' => 'This username is already registered with us, please use a different one'
                ],
                'data' => $customer->getUser()->getUsername()
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => false
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($customer);

            $user = $customer->getUser();
            $user->setUsername($form->get('username')->getData());
            if(trim($form->get('password')->getData()) !== ''){
                $user>setPassword($encoder->encodePassword($user, $form->get('password')->getData()));
            }

            
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_customers_index');
        }

        return $this->render('customers/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="customers_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Customers $customer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            foreach($customer->getOrders() as $order){

            }
            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_customers_index');
    }
}
