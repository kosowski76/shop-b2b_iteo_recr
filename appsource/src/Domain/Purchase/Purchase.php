<?php
namespace App\Domain\Purchase;

use AllowDynamicProperties;
use App\Domain\Client\Client;
use App\Domain\Product\Product;
use App\Infrastructure\Doctrine\PurchaseItemRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Component\Uid\Uuid;

#[AllowDynamicProperties]
class Purchase implements PurchaseRepositoryInterface
{
    protected Uuid $id;
    protected Client $client;
    protected DateTime $createdAt;
    protected int $itemCount = 0;
    protected float $weightTotal = 0.000;
    protected float $grandTotal = 00.000;
/*
 ** This part 'Status' to refactor **/
    protected string $status = "";
    protected Collection $purchaseItems;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->purchaseItems = new ArrayCollection();
        $this->status = 'new';
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
    public function getClient(): Client
    {
        return $this->client;
    }
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
    public function getGrandTotal(): float
    {
        return $this->grandTotal;
    }

    public function setGrandTotal(float $grandTotal): void
    {
        $this->grandTotal = $grandTotal;
    }
    public function getItemCount(): int
    {
        return $this->itemCount;
    }
    public function setItemCount(int $itemCount): void
    {
        $this->itemCount = $itemCount;
    }
    public function getWeightTotal(): float
    {
        return $this->weightTotal;
    }
    public function setWeightTotal(float $weightTotal): void
    {
        $this->weightTotal = $weightTotal;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }
    public function setPurchaseItems(ArrayCollection $purchaseItems): self
    {
        $this->purchaseItems = $purchaseItems;

        return $this;
    }

    /**
     * @param PurchaseItem $purchaseItem
     */
    public function makePurchaseItems(PurchaseItem $purchaseItem): void
    {
        /*
         ** for discussion **/
        $count = $this->getItemCount();
        $this->setItemCount(++$count);

        $weightTotal = $this->getWeightTotal();
        $weightTotal += (float)$purchaseItem->getQuantity() * $purchaseItem->getUnitWeight();
        $this->setWeightTotal($weightTotal);

        $grandTotal = $this->getGrandTotal();
        $grandTotal += (float)$purchaseItem->getQuantity() * $purchaseItem->getUnitPrice();
        $this->setGrandTotal($grandTotal);
    //   $this->setGrandTotal((float)$purchaseItem->getQuantity() * $purchaseItem->getUnitPrice());


        $this->purchaseItems->add($purchaseItem);
    }
}
