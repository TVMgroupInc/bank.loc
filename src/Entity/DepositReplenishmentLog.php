<?php

namespace App\Entity;

use App\Repository\DepositReplenishmentLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepositReplenishmentLogRepository::class)
 * @ORM\Table(name="deposit_replenishment_log")
 */
class DepositReplenishmentLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $sum;

    /**
     * @ORM\ManyToOne(targetEntity=Deposit::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $deposit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(float $sum): self
    {
        $this->sum = $sum;

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
