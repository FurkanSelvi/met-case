<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PreOrderRepository")
 * @ORM\Table(name="pre_orders")
 */
class PreOrder
{
    const STATUS_PENDING = 'pending';
    const STATUS_AUTO_REJECT = 'auto-rejected';
    const STATUS_REJECT = 'rejected';
    const STATUS_APPROVE = 'approved';
    const STATUSES = array(
        self::STATUS_PENDING,
        self::STATUS_REJECT,
        self::STATUS_AUTO_REJECT,
        self::STATUS_APPROVE,
    );
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotNull(message="İsim boş olamaz")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotNull(message="Soyisim boş olamaz")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotNull(message="Email boş olamaz")
     * @Assert\Email(
     * message = "'{{ value }}' adresi geçerli bir mail adresi değildir.",
     * checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull(message="Telefon numarası boş olamaz")
     * @Assert\Regex(pattern="/^[0-9]*$/", message="Geçersiz telefon numarası")
     */
    private $phoneNumber;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Basket")
     */
    private $basket;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice(choices=PreOrder::STATUSES, message="Geçersiz durum.")
     */
    private $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getFirstName(): ?string
    {
        return $this->firstName;
    }

    function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    function getLastName(): ?string
    {
        return $this->lastName;
    }

    function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    function getEmail(): ?string
    {
        return $this->email;
    }

    function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    function getBasket(): ?Basket
    {
        return $this->basket;
    }

    function setBasket(Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }

    function getStatus(): ?string
    {
        return $this->status;
    }

    function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    function getResult()
    {
        return [
            'id' => $this->getId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'email' => $this->getEmail(),
            'phoneNumber' => $this->getPhoneNumber(),
            'status' => $this->getStatus(),
            'basket' => $this->getBasket()->getResult(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }
}
