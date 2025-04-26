<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $carrierName;

    /**
     * @ORM\Column(type="float")
     */
    private $carrierPrice;

    /**
     * @ORM\Column(type="text")
     */
    private $delivery;

    /**
     * @ORM\OneToMany(targetEntity=OrderDetail::class, mappedBy="myOrder", cascade={"persist"}, orphanRemoval=true)
     */
    private $orderDetails;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $stripe_session_id;

    /**
     * 1 : En atente de paiement
     * 2 : Paiement valitée
     * 3 : Expédiée
     */
    
    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    public function getState(): ?int
    {
        return $this->state;
    }
    

    public function setState($state): self
    {
        $this->state = $state;
        return $this;
    }
    

    // fin de orderState

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }

    public function getTotalTva() 
    {
        $totalTva = 0;
        $products = $this->getOrderDetails();
        
        foreach ($products as $product) {
            $coeff = $product->getProductTva()/100;
            $totalTva += $product->getProductPrice() * $coeff;
        }
        return $totalTva;
    }

    public function getTotalwt() 
    {
        $totalTTC = 0;
        $products = $this->getOrderDetails();

        foreach ($products as $product) {
            $coeff = 1 + ($product->getProductTva()/100);
            $totalTTC += ($product->getProductPrice() * $coeff) * $product->getProductQuantity();
        }
        return $totalTTC;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    public function setCarrierName(string $carrierName): self
    {
        $this->carrierName = $carrierName;

        return $this;
    }

    public function getCarrierPrice(): ?float
    {
        return $this->carrierPrice;
    }

    public function setCarrierPrice(float $carrierPrice): self
    {
        $this->carrierPrice = $carrierPrice;

        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    public function setDelivery(string $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails[] = $orderDetail;
            $orderDetail->setMyOrder($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getMyOrder() === $this) {
                $orderDetail->setMyOrder(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripe_session_id;
    }

    public function setStripeSessionId(?string $stripe_session_id): self
    {
        $this->stripe_session_id = $stripe_session_id;

        return $this;
    }
}
