<?php

namespace App\Entity;

use App\Repository\DepositHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepositHistoryRepository::class)
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
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $deposit_id;

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

    public function getDepositId(): ?int
    {
        return $this->deposit_id;
    }

    public function setDepositId(int $deposit_id): self
    {
        $this->deposit_id = $deposit_id;

        return $this;
    }
}
