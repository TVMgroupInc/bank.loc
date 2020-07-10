<?php

namespace App\Entity;

use App\Repository\DepositRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepositRepository::class)
 */
class Deposit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    private $interest_rate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_open;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_close;

    /**
     * @ORM\OneToOne(targetEntity=BankAccount::class, inversedBy="deposit", cascade={"persist", "remove"})
     */
    private $account;

    /**
     * Deposit constructor.
     */
    public function __construct()
    {
        $this->date_open = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInterestRate(): ?string
    {
        return $this->interest_rate;
    }

    public function setInterestRate(string $interest_rate): self
    {
        $this->interest_rate = $interest_rate;

        return $this;
    }

    public function getDateOpen(): ?\DateTimeInterface
    {
        return $this->date_open;
    }

    public function setDateOpen(\DateTimeInterface $date_open): self
    {
        $this->date_open = $date_open;

        return $this;
    }

    public function getDateClose(): ?\DateTimeInterface
    {
        return $this->date_close;
    }

    public function setDateClose(?\DateTimeInterface $date_close): self
    {
        $this->date_close = $date_close;

        return $this;
    }

    public function getAccount(): ?BankAccount
    {
        return $this->account;
    }

    public function setAccount(?BankAccount $account): self
    {
        $this->account = $account;

        return $this;
    }
}
