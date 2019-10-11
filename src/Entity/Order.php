<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="orders")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Basket", inversedBy="orders", cascade={"merge"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $basket;

    /**
     * @Assert\NotNull(message="Adet boş olamaz")
     * @Assert\Positive(message="Geçersiz adet")
     * @ORM\Column(type="integer")
     */
    private $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity($quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getResult()
    {
        return [
            'id' => $this->getId(),
            'product' => $this->getProduct()->getResult(),
            'basket' => $this->getBasket()->getResult(),
            'quantity' => $this->getQuantity()
        ];
    }
}
