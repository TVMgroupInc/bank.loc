<?php

namespace App\Entity;

use App\Repository\DepositHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepositHistoryRepository::class)
 * @ORM\Table(name="deposit_history")
 */
class DepositHistory
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
    private $date_change;

    /**
     * @ORM\ManyToOne(targetEntity=Deposit::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $deposit;

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

    public function getDateChange(): ?\DateTimeInterface
    {
        return $this->date_change;
    }

    public function setDateChange(\DateTimeInterface $date_change): self
    {
        $this->date_change = $date_change;

        return $this;
    }

    public function getDeposit(): ?Deposit
    {
        return $this->deposit;
    }

    public function setDeposit(?Deposit $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }
}
