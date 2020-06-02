<?php

namespace App\Entity;

use App\Repository\RestaurantsUsersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RestaurantsUsersRepository::class)
 */
class RestaurantsUsers
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $restaurantId;

    /**
     * @ORM\OneToOne(targetEntity=Restaurants::class, inversedBy="user")
     */
    private $restaurant;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\OneToOne(targetEntity=Users::class, inversedBy="restaurant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRestaurantId(): ?int
    {
        return $this->restaurantId;
    }

    public function setRestaurantId(int $restaurantId): self
    {
        $this->restaurantId = $restaurantId;

        return $this;
    }

    public function getRestaurant(): ?Restaurants
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurants $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }
}
