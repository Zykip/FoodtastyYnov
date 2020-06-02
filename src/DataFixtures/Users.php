<?php

namespace App\DataFixtures;

use App\Entity\Admins;
use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Users extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        /**
         * Admin User
         */
        $adminUser = new \App\Entity\Users();
        $adminUser->setCreatedAt(Carbon::now())
            ->setIsActive(true)
            ->setUsername('admin')
            ->setSalt(uniqid())
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->encoder->encodePassword($adminUser, 'admin'))
        ;
        $manager->persist($adminUser);

        $admin = new Admins();
        $admin->setEmail('admin@gmail.com')
            ->setName('Admin')
            ->setUser($adminUser)
        ;
        $manager->persist($admin);

        /**
         * Customer
         */
        $customerUser = new \App\Entity\Users();
        $customerUser->setCreatedAt(Carbon::now())
            ->setIsActive(true)
            ->setUsername('customer')
            ->setSalt(uniqid())
            ->setRoles(['ROLE_CUSTOMER'])
            ->setPassword($this->encoder->encodePassword($customerUser, 'customer'))
        ;
        $manager->persist($customerUser);

        $customer = new Admins();
        $customer->setEmail('customer@gmail.com')
            ->setName('Customer')
            ->setUser($customerUser)
        ;
        $manager->persist($customer);

        /**
         * Restaurant User
         */
        $restaurantUser = new \App\Entity\Users();
        $restaurantUser->setCreatedAt(Carbon::now())
            ->setIsActive(true)
            ->setUsername('restaurant')
            ->setSalt(uniqid())
            ->setRoles(['ROLE_RESTAURANT'])
            ->setPassword($this->encoder->encodePassword($restaurantUser, 'restaurant'))
        ;
        $manager->persist($restaurantUser);

        $restaurant = new Admins();
        $restaurant->setEmail('restaurant@gmail.com')
            ->setName('Restaurant Admin')
            ->setUser($restaurantUser)
        ;
        $manager->persist($restaurant);

        $manager->flush();
    }
}
