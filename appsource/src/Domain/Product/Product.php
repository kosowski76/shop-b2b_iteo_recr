<?php
namespace App\Domain\Product;

use Symfony\Component\Uid\Uuid;

class Product implements ProductRepositoryInterface
{
    protected Uuid $id;
    protected string $name;
    protected string $description= "";
    protected float $weight = 0.000;
    protected float $purchasePrice = 0.000;
    protected float $sellingPrice = 0.000;
    protected int $taxRate = 0;

    public function __constructor()
    {

    }
    public function getId(): Uuid
    {
        return $this->id;
    }
    public function setId(Uuid $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    public function getWeight(): float
    {
        return $this->weight;
    }
    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }
    public function getPurchasePrice(): float
    {
        return $this->purchasePrice;
    }
    public function setPurchasePrice(float $purchasePrice): void
    {
        $this->purchasePrice = $purchasePrice;
    }
    public function getSellingPrice(): float
    {
        return $this->sellingPrice;
    }
    public function setSellingPrice(float $sellingPrice): void
    {
        $this->sellingPrice = $sellingPrice;
    }
    public function getTaxRate(): int
    {
        return $this->taxRate;
    }
    public function setTaxRate(int $taxRate): void
    {
        $this->taxRate = $taxRate;
    }
}
