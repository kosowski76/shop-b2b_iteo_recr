<?php
namespace App\Application\Handler\Purchase;

use App\Domain\Purchase\Purchase;
use App\Domain\PurchaseItem\PurchaseItemRepositoryInterface;
use Exception;

class ListPurchaseItemsHandler
{
    protected PurchaseItemRepositoryInterface $purchaseItemRepository;

    public function __construct(
        PurchaseItemRepositoryInterface $purchaseItemRepository
    ){
        $this->purchaseItemRepository = $purchaseItemRepository;
    }

    /**
     * @param Purchase $purchase
     * @return array
     * @throws Exception
     **/
    public function handle(Purchase $purchase): array
    {
        return $this->purchaseItemRepository->findBy(['purchase' => $purchase]);
    }
}