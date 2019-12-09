<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $subTotal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $childernDiscount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $additionalDiscount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $couponDiscount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0, nullable=true)
     */
    private $discountAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0, nullable=true)
     */
    private $total;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Coupon", inversedBy="invoice", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="coupon_id", referencedColumnName="id", nullable=true)
     */
    private $couponId;

    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InvoiceItem", mappedBy="invoiceId")
     */
    private $invoiceItem;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function __construct()
    {
        $this->invoiceItem = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubTotal(): ?string
    {
        return $this->subTotal;
    }

    public function setSubTotal(string $subTotal): self
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getChildernDiscount(): ?int
    {
        return $this->childernDiscount;
    }

    public function setChildernDiscount(?int $childernDiscount): self
    {
        $this->childernDiscount = $childernDiscount;

        return $this;
    }

    public function getAdditionalDiscount(): ?int
    {
        return $this->additionalDiscount;
    }

    public function setAdditionalDiscount(?int $additionalDiscount): self
    {
        $this->additionalDiscount = $additionalDiscount;

        return $this;
    }

    public function getCouponDiscount(): ?int
    {
        return $this->couponDiscount;
    }

    public function setCouponDiscount(?int $couponDiscount): self
    {
        $this->couponDiscount = $couponDiscount;

        return $this;
    }

    public function getDiscountAmount(): ?string
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?string $discountAmount): self
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getCouponId(): ?Coupon
    {
        return $this->couponId;
    }

    public function setCouponId(?Coupon $couponId): self
    {
        $this->couponId = $couponId;

        return $this;
    }

    /**
     * @return Collection|InvoiceItem[]
     */
    public function getInvoiceItem(): Collection
    {
        return $this->invoiceItem;
    }

    public function addInvoiceItem(InvoiceItem $invoiceItem): self
    {
        if (!$this->invoiceItem->contains($invoiceItem)) {
            $this->invoiceItem[] = $invoiceItem;
            $invoiceItem->setInvoiceId($this);
        }

        return $this;
    }

    public function removeInvoiceItem(InvoiceItem $invoiceItem): self
    {
        if ($this->invoiceItem->contains($invoiceItem)) {
            $this->invoiceItem->removeElement($invoiceItem);
            // set the owning side to null (unless already changed)
            if ($invoiceItem->getInvoiceId() === $this) {
                $invoiceItem->setInvoiceId(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    
}
