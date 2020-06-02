<?php

namespace App\Entity;

use App\Repository\CartDishesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartDishesRepository::class)
 */
class CartDishes
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
    private $dishId;

    /**
     * @ORM\ManyToOne(targetEntity=Dishes::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $dish;

    /**
     * @ORM\Column(type="integer")
     */
    private $restaurantId;

    /**
     * @ORM\ManyToOne(targetEntity=Restaurants::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userAgent;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ipAddress;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="dishes")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDishId(): ?int
    {
        return $this->dishId;
    }

    public function setDishId(int $dishId): self
    {
        $this->dishId = $dishId;

        return $this;
    }

    public function getDish(): ?Dishes
    {
        return $this->dish;
    }

    public function setDish(?Dishes $dish): self
    {
        $this->dish = $dish;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
