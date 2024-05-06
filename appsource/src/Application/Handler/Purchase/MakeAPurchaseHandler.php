<?php
namespace App\Application\Handler\Purchase;

//use App\Application\Exceptions\createPurchaseException;
use App\Domain\Client\ClientRepositoryInterface;
use App\Domain\Product\ProductRepositoryInterface;
use App\Domain\Purchase\Purchase;
use App\Domain\Purchase\PurchaseItem;
use App\Domain\Purchase\PurchaseRepositoryInterface;
use App\Domain\PurchaseItem\PurchaseItemRepositoryInterface;
use DateTime;
use Exception;
use Symfony\Component\Uid\Uuid;

class MakeAPurchaseHandler
{
    protected ClientRepositoryInterface $clientRepository;
    protected ProductRepositoryInterface $productRepository;
    protected PurchaseItemRepositoryInterface $purchaseItemRepository;
    protected PurchaseRepositoryInterface $purchaseRepository;

    protected Purchase $purchase;

    public function __construct(
        ClientRepositoryInterface   $clientRepository,
        ProductRepositoryInterface $productRepository,
        PurchaseItemRepositoryInterface $purchaseItemRepository,
        PurchaseRepositoryInterface $purchaseRepository,
    )
    {
        $this->clientRepository = $clientRepository;
        $this->productRepository = $productRepository;
        $this->purchaseItemRepository = $purchaseItemRepository;
        $this->purchaseRepository = $purchaseRepository;
        $this->purchase = new Purchase();
    }

    /**
     * @param array $itemsArray
     * @throws Exception
     */
    public function handle(array $itemsArray): void
    {
        $client = $this->clientRepository->findOneBy(
            ['username' => $itemsArray['username']]
        );
        $purchase = new Purchase();
        $purchase->setId(Uuid::v7());
        $purchase->setClient($client);
        $purchase->setCreatedAt(new DateTime());
        $this->purchase = $purchase;

        try
        { $this->purchaseRepository->save($this->purchase); }
        catch (Exception $exception)
        {
            throw new Exception('Order can not be saved.'.PHP_EOL.
                'Error message: '.$exception->getMessage());
        }


        foreach($itemsArray['purchaseItems'][0] as $item)
        {
            $product = $this->productRepository->findOneBy(['id' => $item['productId']]);
            if(!$product)
                { throw new Exception('No product in database.'); }

            $purchaseItem = new PurchaseItem();
            $purchaseItem->setId(Uuid::v7());
            $purchaseItem->setPurchase($this->purchase);
            $purchaseItem->setProduct($product);
            $purchaseItem->setQuantity($item['quantity']);
            $purchaseItem->setUnitWeight($product->getWeight());
            $purchaseItem->setUnitPrice($product->getSellingPrice());
            $purchaseItem->setTaxRate($product->getTaxRate());

            try
            {
                $this->purchaseItemRepository->save($purchaseItem);
                $this->purchase->makePurchaseItems($purchaseItem);
                $this->purchaseRepository->save($this->purchase);
            }
            catch (Exception $exception)
            {
                throw new Exception('Order Item can not be saved.'.PHP_EOL.
                'Error message: '.$exception->getMessage());
            }
        }
    }

}
