<?php
namespace App\Domain\Purchase;

use App\Domain\Product\Product;
use Symfony\Component\Uid\Uuid;

class PurchaseItem
{
    private Uuid $id;
    protected Purchase $purchase;
    protected Product $product;
    protected float $quantity;
    protected float $unitWeight;
    protected int $taxRate = 0;
    protected float $unitPrice;

    public function getId(): Uuid
    {
        return $this->id;
    }
    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }
    public function getPurchase(): Purchase
    {
        return $this->purchase;
    }
    public function setPurchase(Purchase $purchase): void
    {
        $this->purchase = $purchase;
    }
    public function getProduct(): Product
    {
        return $this->product;
    }
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }
    public function getQuantity(): int
    {
        return $this->quantity;
    }
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
    public function getUnitWeight(): float
    {
        return $this->unitWeight;
    }
    public function setUnitWeight(float $unitWeight): void
    {
        $this->unitWeight = $unitWeight;
    }
    public function getTaxRate(): int
    {
        return $this->taxRate;
    }
    public function setTaxRate(int $taxRate): void
    {
        $this->taxRate = $taxRate;
    }
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }
    public function setUnitPrice(float $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }
}