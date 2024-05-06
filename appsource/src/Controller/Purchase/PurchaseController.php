<?php
namespace App\Controller\Purchase;

use App\Application\Handler\Purchase\ListPurchaseClientHandler;
use App\Application\Handler\Purchase\MakeAPurchaseHandler;
use App\Application\Verification\PurchaseVerifier\PurchaseVerificationCriteria1;
use App\Application\Verification\PurchaseVerifier\VerificationBalanceForPurchase;
use App\Application\Verification\PurchaseVerifier\VerificationPurchaseQuantity;
use App\Application\Verification\PurchaseVerifier\VerificationPurchaseWeight;
use App\Domain\BankAccount\BankAccountRepositoryInterface;
use App\Domain\BankAccount\FirmBankAccountRepositoryInterface;
use App\Domain\Purchase\Purchase;
use App\Domain\Purchase\PurchaseItem;
use App\Infrastructure\Doctrine\ClientRepository;
use App\Infrastructure\Doctrine\ProductRepository;
use App\Infrastructure\Doctrine\PurchaseItemRepository;
use Exception;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class PurchaseController extends AbstractController
{
    protected ListPurchaseClientHandler $listPurchaseClientHandler;
//    protected ListPurchaseItemsHandler $listPurchaseItemsHandler;
    protected MakeAPurchaseHandler $purchaseHandler;
    protected PurchaseItemRepository $purchaseItemRepository;
    protected Security $security;

    protected ClientRepository $clientRepository;

    protected ProductRepository $productRepository;
    protected FirmBankAccountRepositoryInterface $bankAccountRepository;
    protected int $limitQuantity = 5;
    protected float $limitWeight = 24.000;

    public function __construct(
        MakeAPurchaseHandler $purchaseHandler,
        ListPurchaseClientHandler $listPurchaseClientHandler,
    /*    ListPurchaseItemsHandler $listPurchaseItemsHandler, */
        PurchaseItemRepository $purchaseItemRepository,
        Security $security,
        ClientRepository $clientRepository,
        ProductRepository $productRepository,
        FirmBankAccountRepositoryInterface $bankAccountRepository
    ) {
        $this->listPurchaseClientHandler = $listPurchaseClientHandler;
    //    $this->listPurchaseItemsHandler = $listPurchaseItemsHandler;
        $this->purchaseHandler = $purchaseHandler;
        $this->purchaseItemRepository = $purchaseItemRepository;
        $this->security = $security;

        $this->clientRepository = $clientRepository;
        $this->productRepository = $productRepository;
        $this->bankAccountRepository = $bankAccountRepository;
    }

    #[Route('/api/order', name: 'api_purchase_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $allPurchases = [];
        $purchases = $this->listPurchaseClientHandler->handle();

        foreach ($purchases as $purchase)
        {
            $allPurchases[] = [
                'order_id' => $purchase->getId(),
                'client_id' => $purchase->getClient()->getId(),
                'created_at' => $purchase->getCreatedAt(),
                'item_count' => $purchase->getItemCount(),
                'weight_total' => $purchase->getWeightTotal(),
                'grand_total_net' => $purchase->getGrandTotal(),
                'products' => $this->hasPurchaseItems($purchase ?? null)
            ];

        }

        return new JsonResponse($allPurchases, Response::HTTP_OK);
    }

    #[Route('/api/order', name: 'api_purchase_create', methods: ['POST'])]
    public function createPurchase(Request $request): JsonResponse
    {
        $purchaseItemsArray = json_decode($request->getContent(), true);
        if($purchaseItemsArray === false || $purchaseItemsArray === null) {
            $result = [
                'message' => 'Invalid json Request body',
                'status_code' => Response::HTTP_BAD_REQUEST];
            $statusCode = Response::HTTP_BAD_REQUEST;
            return new JsonResponse($result, $statusCode);
        }

        /***
         * get Client ***/
        try {
            $client = $this->clientRepository->findOneBy(
                ['username' => $this->security->getUser()->getUserIdentifier()]);
        } catch (Exception $exception)
        {
            throw new Exception('No this Client in database'.PHP_EOL.
                'Error message: '.$exception->getMessage()); }

        $purchase = new Purchase();
        $purchaseEntity = Uuid::v7();
        $purchase->setId($purchaseEntity);
        $purchase->setClient($client);
        $purchase->setCreatedAt(new DateTime());

        foreach ($purchaseItemsArray as $item)
        {
            /***
             * make PurchaseItem ***/
            $purchaseItem = new PurchaseItem();
            $purchaseItemEntity = Uuid::v7();
            $purchaseItem->setId($purchaseItemEntity);
            $purchaseItem->setPurchase($purchase);
            /***
             * get Product ***/
            try {
                $product = $this->productRepository->findOneBy(['id' => $item['productId']]);
            } catch (Exception $exception)
            {
                throw new Exception('No this product in database'.PHP_EOL.
                'Error message: '.$exception->getMessage()); }

            $purchaseItem->setProduct($product);
            $purchaseItem->setQuantity($item['quantity']);
            $purchaseItem->setUnitWeight($product->getWeight());
            $purchaseItem->setTaxRate($product->getTaxRate());
            $purchaseItem->setUnitPrice($product->getSellingPrice());

            $purchase->makePurchaseItems($purchaseItem);
            unset($purchaseItem);
        }

        try {
            $bankAccount = $this->bankAccountRepository->findOneBy(
                ['client' => $purchase->getClient()]);
        } catch (Exception $exception)
        {
            throw new Exception('No this BankAccount in database'.PHP_EOL.
                'Error message: '.$exception->getMessage()); }

        /** verification purchase Verificator **/
        if(!$this->purchaseVerification($purchase, $bankAccount))
        {
            $result = [
                'message' => 'Order can not allowed to created',
                'status_code' => Response::HTTP_BAD_REQUEST];
            $statusCode = Response::HTTP_BAD_REQUEST;
            return new JsonResponse($result, $statusCode);
        }





        $this->purchaseHandler->handle(
            [
                'username' => $this->security->getUser()->getUserIdentifier(),
                'purchaseItems' => [$purchaseItemsArray],
            ]
        );

        $result = [
            'message' => 'Order created',
            'status_code' => Response::HTTP_CREATED];
        $statusCode = Response::HTTP_CREATED;
        return new JsonResponse($result, $statusCode);
    }

    protected function hasPurchaseItems(Purchase $purchase): array
    {
        $allPurchaseItems = array();
        $purchaseItems = $this->purchaseItemRepository->findBy(['purchase' => $purchase]);

        /** @var PurchaseItem $purchaseItem */
        foreach ($purchaseItems as $purchaseItem) {
            $allPurchaseItems[] = [
                'product_id' => $purchaseItem->getProduct()->getId(),
                'quantity' => $purchaseItem->getQuantity(),
                'unit_weight' => $purchaseItem->getUnitWeight(),
                'tax_rate' => $purchaseItem->getTaxRate(),
                'unit_price' => $purchaseItem->getUnitPrice()
            ];
        }

        return $allPurchaseItems;
    }

    protected function purchaseVerification(Purchase $purchase, BankAccountRepositoryInterface $bankAccount): bool
    {
        /*
         * The test code works with all verification via
         * the base purchase Verification interface for true
         */
        $verifier = new PurchaseVerificationCriteria1($purchase, $bankAccount);

        $checkPurchaseQuantity = new VerificationPurchaseQuantity();
        $checkPurchaseQuantity->verification($verifier);

        $checkPurchaseWeight = new VerificationPurchaseWeight();
        $checkPurchaseWeight->verification($verifier);

        $checkBalance = new VerificationBalanceForPurchase();
        $checkBalance->verification($verifier);

        $checkPurchaseQuantity->setLimit($this->limitQuantity);
        $checkPurchaseWeight->setLimit($this->limitWeight);
        $checkBalance->setLimit($purchase->getGrandTotal());

        return $verifier->isAllowed();
    }
}
