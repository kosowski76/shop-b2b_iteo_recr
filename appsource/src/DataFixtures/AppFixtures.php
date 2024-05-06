<?php
namespace App\DataFixtures;

use App\Domain\BankAccount\FirmBankAccount;
use App\Domain\Client\Client;
use App\Domain\Client\ClientRepositoryInterface;
use App\Domain\Product\ProductRepositoryInterface;
use App\Domain\Purchase\Purchase;
use App\Domain\Product\Product;
use App\Domain\Purchase\PurchaseItem;
use App\Domain\Purchase\PurchaseRepositoryInterface;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    protected ClientRepositoryInterface $clientRepository;
    protected ProductRepositoryInterface $productRepository;
    protected PurchaseRepositoryInterface $purchaseRepository;

    public function __construct(
        UserPasswordHasherInterface $hasher,
        ClientRepositoryInterface $clientRepository,
        ProductRepositoryInterface $productRepository,
        PurchaseRepositoryInterface $purchaseRepository
    ) {
        $this->hasher = $hasher;
        $this->clientRepository = $clientRepository;
        $this->productRepository = $productRepository;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $clientValues = array(
            [
                'Id'    => '018F2522-5E4D-7C2A-BB9C-E8C0AEA8A77E',
                'Email' => 'client@test.com',
                'Name'  => 'client', 'Password' => '123123'
            ],
            [
                'Id'    => '018F2A27-1E4C-4C2D-BF9C-E8C0AEA8E39A',
                'Email' => 'admin@test.com',
                'Name'  => 'admin', 'Password' => '321ewq'
            ]);

        foreach ($clientValues as $value)
        {
            /***
             * Client ***/
            $client = new Client();
            $clientEntity = Uuid::fromString($value['Id']);
            $client->setId($clientEntity);
            $client->setEmail($value['Email']);
            $client->setUsername($value['Name']);
            $password = $this->hasher->hashPassword($client, $value['Password']);
            $client->setPassword($password);
            $client->setCreatedAt(new DateTime());

           $manager->persist($client);
           $manager->flush();
        }

        $bankAccountValues = array(
            [
                'Id'        => '018F2521-8C87-71EF-B326-054F4DEE82E0',
                'ClientId'  => '018F2522-5E4D-7C2A-BB9C-E8C0AEA8A77E',
                'Name'      => 'Firm Bank Account of Company',
                'Balance'   => 21732.347
            ]);

        foreach($bankAccountValues as $value)
        {
            /***
             * get Client ***/
            try {
                $client = $this->clientRepository->findOneBy(
                    ['id' => Uuid::fromString($value['ClientId'])]);
            } catch (Exception $exception)
            {
                throw new Exception('No this Client in database'.PHP_EOL.
                    'Error message: '.$exception->getMessage()); }
            /***
             * FirmBankAccount ***/
            $bankAccount = new FirmBankAccount();
            $bankAccountEntity = Uuid::fromString($value['Id']);
            $bankAccount->setId($bankAccountEntity);
            $bankAccount->setName($value['Name']);
            $bankAccount->setClient($client);
            $bankAccount->setBalance($value['Balance']);

            $manager->persist($bankAccount);
            $manager->flush();

            unset($bankAccount);
            unset($client);
        }

        /***
         * Products ***/
        $productValues = array(
            [
                'Id' => '018F231E-BC45-75F4-8CEE-3A94A1866619', 'Name' => 'Coal',
                'Weight' => 1.000, 'PurchasePrice' => 900.00,
                'SellingPrice' => 1107.00, 'TaxRate' => 7,
            ],
            [
                'Id' => '018F231E-BC45-75F4-8CEE-3A94A3595D4C', 'Name' => 'Sand',
                'Weight' => 1.100, 'PurchasePrice' => 16.00,
                'SellingPrice' => 19.68, 'TaxRate' => 23,
            ],
            [
                'Id' => '018F231E-BC45-75F4-8CEE-3A94A28C8C19', 'Name' => 'Rye',
                'Weight' => 1.000, 'PurchasePrice' => 750.00,
                'SellingPrice' => 922.50, 'TaxRate' => 7,
            ],
            [
                'Id' => '018F2522-6051-7B82-B7C8-7520D18A5292', 'Name' => 'Wheat',
                'Weight' => 1.050, 'PurchasePrice' => 760.00,
                'SellingPrice' => 934.80, 'TaxRate' => 7,
            ],
            [
                'Id' => '018F2522-6051-7B82-B7C8-7520CEEFF96C', 'Name' => 'Cement',
                'Weight' => 1.000, 'PurchasePrice' => 600.00,
                'SellingPrice' => 738.00, 'TaxRate' => 7,
            ],
            [
                'Id' => '018F2522-6051-7B82-B7C8-7520D00D1162', 'Name' => 'Graver',
                'Weight' => 1.200, 'PurchasePrice' => 25.00,
                'SellingPrice' => 30.00, 'TaxRate' => 23,
            ]
        );

        foreach ($productValues as $value)
        {   /***
             * Product ***/
            $product = new Product();
            $productEntity = Uuid::fromString($value['Id']);
            $product->setId($productEntity);
            $product->setName($value['Name']);
            $product->setWeight($value['Weight']);
            $product->setPurchasePrice($value['PurchasePrice']);
            $product->setSellingPrice($value['SellingPrice']);
            $product->setTaxRate($value['TaxRate']);

            $manager->persist($product);
            $manager->flush();

            unset($product);
        }

        /***
         * get Client ***/
        try {
            $client = $this->clientRepository->findOneBy(
                ['id' => Uuid::fromString('018F2522-5E4D-7C2A-BB9C-E8C0AEA8A77E')]);
        }
        catch (Exception $exception)
        {
            throw new Exception('No this Client in database'.PHP_EOL.
                'Error message: '.$exception->getMessage()); }
        /***
         * Purchase ***/
        $purchase = new Purchase();// PurchaseId:018F2521-8C87-71EF-B326-054F4CE62083
        $purchaseEntity = Uuid::fromString('018F2521-8C87-71EF-B326-054F4CE62083');
        $purchase->setId($purchaseEntity);
        $purchase->setClient($client);
        $purchase->setCreatedAt(new DateTime());

        $manager->persist($purchase);
        $manager->flush();

        unset($purchase);

        $purchaseItemValues = array(
            [
                'PurchaseItemId' => '018F2521-8C87-71EF-B326-054F4B24AB2A',
                'PurchaseId' => '018F2521-8C87-71EF-B326-054F4CE62083',
                'ProductId' => '018F2522-6051-7B82-B7C8-7520D00D1162', 'Quantity' => 5,
            ],
            [
                'PurchaseItemId' => '018F2521-8C87-71EF-B326-0C7F4B24ABF1',
                'PurchaseId' => '018F2521-8C87-71EF-B326-054F4CE62083',
                'ProductId' => '018F2522-6051-7B82-B7C8-7520D18A5292', 'Quantity' => 2,
            ]
        );

        /***
         * get Purchase ***/
        try {
            $purchase = $this->purchaseRepository->findOneBy(['id' => '018F2521-8C87-71EF-B326-054F4CE62083']);
        } catch (Exception $exception)
        { throw new Exception('No this Purchase in database'.PHP_EOL.
                'Error message: '.$exception->getMessage()); }

        foreach ($purchaseItemValues as $itemValue)
        {
            /***
             * make PurchaseItem ***/
            $purchaseItem = new PurchaseItem();
            $purchaseItemEntity = Uuid::fromString($itemValue['PurchaseItemId']);
            $purchaseItem->setId($purchaseItemEntity);
            $purchaseItem->setPurchase($purchase);
            /***
             * get Product ***/
            try {
                $product = $this->productRepository->findOneBy(['id' => $itemValue['ProductId']]);
            } catch (Exception $exception)
                { throw new Exception('No this product in database'.PHP_EOL.
                'Error message: '.$exception->getMessage()); }
            $purchaseItem->setProduct($product);
            $purchaseItem->setQuantity($itemValue['Quantity']);
            $purchaseItem->setUnitWeight($product->getWeight());
            $purchaseItem->setTaxRate($product->getTaxRate());
            $purchaseItem->setUnitPrice($product->getSellingPrice());
            $manager->persist($purchaseItem);

            $manager->flush();

            $purchase->makePurchaseItems($purchaseItem);
            $manager->persist($purchase);

            $manager->flush();
            unset($purchaseItem);
        }

        $manager->flush();
    }
}
