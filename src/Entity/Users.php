<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $verificationToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\OneToOne(targetEntity=RestaurantsUsers::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $restaurant;

    /**
     * @ORM\OneToOne(targetEntity=Admins::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $admin;

    /**
     * @ORM\OneToOne(targetEntity=Customers::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity=CartDishes::class, mappedBy="user")
     */
    private $dishes;

    /**
     * @ORM\OneToMany(targetEntity=Reviews::class, mappedBy="user")
     */
    private $reviews;

    public function __construct()
    {
        $this->dishes = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;

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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getAdmin(): ?Admins
    {
        return $this->admin;
    }

    public function setAdmin(Admins $admin): self
    {
        $this->admin = $admin;

        // set the owning side of the relation if necessary
        if ($admin->getUser() !== $this) {
            $admin->setUser($this);
        }

        return $this;
    }

    public function getCustomer(): ?Customers
    {
        return $this->customer;
    }

    public function setCustomer(Customers $customer): self
    {
        $this->customer = $customer;

        // set the owning side of the relation if necessary
        if ($customer->getUser() !== $this) {
            $customer->setUser($this);
        }

        return $this;
    }

    public function getRestaurant(): ?RestaurantsUsers
    {
        return $this->restaurant;
    }

    public function setRestaurant(?RestaurantsUsers $restaurant): self
    {
        $this->restaurant = $restaurant;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $restaurant ? null : $this;
        if ($restaurant->getUser() !== $newUser) {
            $restaurant->setUser($newUser);
        }

        return $this;
    }

    /**
     * @return Collection|CartDishes[]
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(CartDishes $dish): self
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes[] = $dish;
            $dish->setUser($this);
        }

        return $this;
    }

    public function removeDish(CartDishes $dish): self
    {
        if ($this->dishes->contains($dish)) {
            $this->dishes->removeElement($dish);
            // set the owning side to null (unless already changed)
            if ($dish->getUser() === $this) {
                $dish->setUser(null);
            }
        }

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
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }
}
