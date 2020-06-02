<?php

namespace App\Entity;

use App\Repository\DishesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DishesRepository::class)
 */
class Dishes
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
    private $mediaId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $restaurantId;

    /**
     * @ORM\ManyToOne(targetEntity=Restaurants::class, inversedBy="dishes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=OrdersDishes::class, mappedBy="dish")
     */
    private $ordersDishes;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

    /**
     * @ORM\OneToMany(targetEntity=Reviews::class, mappedBy="dish")
     */
    private $reviews;

    public function __construct()
    {
        $this->ordersDishes = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMediaId(): ?int
    {
        return $this->mediaId;
    }

    public function setMediaId(int $mediaId): self
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|OrdersDishes[]
     */
    public function getOrdersDishes(): Collection
    {
        return $this->ordersDishes;
    }

    public function addOrdersDish(OrdersDishes $ordersDish): self
    {
        if (!$this->ordersDishes->contains($ordersDish)) {
            $this->ordersDishes[] = $ordersDish;
            $ordersDish->setDish($this);
        }

        return $this;
    }

    public function removeOrdersDish(OrdersDishes $ordersDish): self
    {
        if ($this->ordersDishes->contains($ordersDish)) {
            $this->ordersDishes->removeElement($ordersDish);
            // set the owning side to null (unless already changed)
            if ($ordersDish->getDish() === $this) {
                $ordersDish->setDish(null);
            }
        }

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return Collection|Reviews[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setDish($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getDish() === $this) {
                $review->setDish(null);
            }
        }

        return $this;
    }
}
