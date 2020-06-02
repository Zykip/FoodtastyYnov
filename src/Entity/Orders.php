<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdersRepository::class)
 */
class Orders
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
    private $customerId;

    /**
     * @ORM\ManyToOne(targetEntity=Customers::class, inversedBy="orders")
     */
    private $customer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deliverAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $orderNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customerAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customerPhone;

    /**
     * @ORM\OneToMany(targetEntity=OrdersDishes::class, mappedBy="order")
     */
    private $dishes;

    /**
     * @ORM\Column(type="integer")
     */
    private $restaurantId;

    /**
     * @ORM\ManyToOne(targetEntity=Restaurants::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurant;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $deliveryCost;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDelivered;

    public function __construct()
    {
        $this->dishes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomer(): ?Customers
    {
        return $this->customer;
    }

    public function setCustomer(?Customers $customer): self
    {
        $this->customer = $customer;

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

    public function getDeliverAt(): ?\DateTimeInterface
    {
        return $this->deliverAt;
    }

    public function setDeliverAt(\DateTimeInterface $deliverAt): self
    {
        $this->deliverAt = $deliverAt;

        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress(?string $customerAddress): self
    {
        $this->customerAddress = $customerAddress;

        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(string $customerPhone): self
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    /**
     * @return Collection|OrdersDishes[]
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(OrdersDishes $dish): self
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes[] = $dish;
            $dish->setOrder($this);
        }

        return $this;
    }

    public function removeDish(OrdersDishes $dish): self
    {
        if ($this->dishes->contains($dish)) {
            $this->dishes->removeElement($dish);
            // set the owning side to null (unless already changed)
            if ($dish->getOrder() === $this) {
                $dish->setOrder(null);
            }
        }

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

    public function getDeliveryCost(): ?string
    {
        return $this->deliveryCost;
    }

    public function setDeliveryCost(string $deliveryCost): self
    {
        $this->deliveryCost = $deliveryCost;

        return $this;
    }

    public function getIsDelivered(): ?bool
    {
        return $this->isDelivered;
    }

    public function setIsDelivered(bool $isDelivered): self
    {
        $this->isDelivered = $isDelivered;

        return $this;
    }
}
