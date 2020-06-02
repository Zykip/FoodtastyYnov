<?php

namespace App\Entity;

use App\Repository\OrdersDishesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdersDishesRepository::class)
 */
class OrdersDishes
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
     * @ORM\ManyToOne(targetEntity=Dishes::class, inversedBy="ordersDishes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dish;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderId;

    /**
     * @ORM\ManyToOne(targetEntity=Orders::class, inversedBy="dishes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

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

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getOrder(): ?Orders
    {
        return $this->order;
    }

    public function setOrder(?Orders $order): self
    {
        $this->order = $order;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
