<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CouponRepository")
 */
class Coupon
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
    private $couponNumber;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Invoice", mappedBy="couponId", cascade={"persist", "remove"})
     */
    private $invoice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCouponNumber(): ?int
    {
        return $this->couponNumber;
    }

    public function setCouponNumber(int $couponNumber): self
    {
        $this->couponNumber = $couponNumber;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        // set (or unset) the owning side of the relation if necessary
        $newCouponId = null === $invoice ? null : $this;
        if ($invoice->getCouponId() !== $newCouponId) {
            $invoice->setCouponId($newCouponId);
        }

        return $this;
    }
}
