<?php

namespace App\Entity;

use App\Repository\BankAccountLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BankAccountLogRepository::class)
 */
class BankAccountLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $balance_change;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_ops;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $type_ops;

    /**
     * @ORM\ManyToOne(targetEntity=BankAccount::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $bank_account;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalanceChange(): ?float
    {
        return $this->balance_change;
    }

    public function setBalanceChange(float $balance_change): self
    {
        $this->balance_change = $balance_change;

        return $this;
    }

    public function getDateOps(): ?\DateTimeInterface
    {
        return $this->date_ops;
    }

    public function setDateOps(\DateTimeInterface $date_ops): self
    {
        $this->date_ops = $date_ops;

        return $this;
    }

    public function getTypeOps(): ?string
    {
        return $this->type_ops;
    }

    public function setTypeOps(string $type_ops): self
    {
        $this->type_ops = $type_ops;

        return $this;
    }

    public function getBankAccount(): ?BankAccount
    {
        return $this->bank_account;
    }

    public function setBankAccount(?BankAccount $bank_account): self
    {
        $this->bank_account = $bank_account;

        return $this;
    }
}
