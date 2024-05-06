<?php
namespace App\Tests\Unit\Application\Verification;

use App\Application\Verification\PurchaseVerifier\VerificationBalanceForPurchase;
use App\Application\Verification\PurchaseVerifier\VerificationPurchaseQuantity;
use App\Application\Verification\PurchaseVerifier\VerificationPurchaseWeight;
use App\Application\Verification\PurchaseVerifier\PurchaseVerificationCriteria1;
use App\Application\Verification\PurchaseVerifier\PurchaseVerifier;
use App\Domain\BankAccount\FirmBankAccount;
use App\Domain\Client\Client;
use App\Domain\Purchase\Purchase;
use App\Domain\Purchase\PurchaseItem;
use App\Domain\Product\Product;
use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Uid\Uuid;

class purchaseVerificationUnitTest extends TestCase
{
    protected function generateUuid(): Uuid
    { return Uuid::v7(); /* 018f0326-4186-769a-8a6c-78876c0d76bc */}
    public function test_PurchaseVerification_ShouldReturn_true(): void
    {

        $client = new Client();
        $client->setId($this->generateUuid());
        $client->setEmail('client@test.com');
        $client->setUsername('tester');
        $client->setPassword('123123');
        $client->setCreatedAt(new DateTime('now'));

        $purchase = new Purchase();
        $purchase->setId($this->generateUuid());
        $purchase->setClient($client);
        $purchase->setGrandTotal(356.60);
        $purchase->setItemCount(5);
        $purchase->setWeightTotal(24.0);

        $bankAccount = new FirmBankAccount("SomeOne", $client);
        $bankAccount->setBalance(356.61);

        echo "The test code works with all verification via the base purchase Verification interface for true\n";
        $verifier1 = new PurchaseVerificationCriteria1($purchase, $bankAccount);

        $checkPurchaseQuantity = new VerificationPurchaseQuantity();
        $checkPurchaseQuantity->verification($verifier1);

        $checkPurchaseWeight = new VerificationPurchaseWeight();
        $checkPurchaseWeight->verification($verifier1);

        $checkBalance = new VerificationBalanceForPurchase();
        $checkBalance->verification($verifier1);

        $criteria = new stdClass();
        $configTrue = array();
        $criteria->limitQuantity = 0.00; $criteria->limitWeight = 24.00; $criteria->limitBalance = 356.60;
        $configTrue[] = $criteria;
        $criteria->limitQuantity = 5; $criteria->limitWeight = 24.00; $criteria->limitBalance = 356.60;
        $configTrue[] = $criteria;
        $criteria->limitQuantity = 4; $criteria->limitWeight = 24; $criteria->limitBalance = 356.60;
        $configTrue[] = $criteria;
        $criteria->limitQuantity = 5; $criteria->limitWeight = 26; $criteria->limitBalance = 356.59;
        $configTrue[] = $criteria;

        foreach($configTrue as $config)
        {
            $checkPurchaseQuantity->setLimit($config->limitQuantity);
            $checkPurchaseWeight->setLimit($config->limitWeight);
            $checkBalance->setLimit($config->limitBalance);

            $check = $verifier1->isAllowed();
            $this->assertTrue($check);
        }
    }
    public function test_PurchaseVerification_ShouldReturn_false(): void
    {
        $client = new Client();
        $client->setId($this->generateUuid());
        $client->setEmail('client@test.com');
        $client->setUsername('tester');
        $client->setPassword('123123');
        $client->setCreatedAt(new DateTime('now'));

        $purchase = new Purchase();
        $purchase->setId($this->generateUuid());
        $purchase->setClient($client);
        $purchase->setGrandTotal(356.60);
        $purchase->setItemCount(5);
        $purchase->setWeightTotal(24.0);

        $bankAccount = new FirmBankAccount("SomeOne", $client);
        $bankAccount->setBalance(356.61);

        echo "The test code works with all verification via the base purchase Verification interface for false\n";
        $verifier1 = new PurchaseVerificationCriteria1($purchase, $bankAccount);

        $checkPurchaseQuantity = new VerificationPurchaseQuantity();
        $checkPurchaseQuantity->verification($verifier1);

        $checkPurchaseWeight = new VerificationPurchaseWeight();
        $checkPurchaseWeight->verification($verifier1);

        $checkBalance = new VerificationBalanceForPurchase();
        $checkBalance->verification($verifier1);

        $criteria = new stdClass();
        $configFalse = array();

        $criteria->limitQuantity = 0.00; $criteria->limitWeight = 0.00; $criteria->limitBalance = 356.62;
        $configFalse[] = $criteria;
        $criteria->limitQuantity = 5; $criteria->limitWeight = 24.00; $criteria->limitBalance = 356.62;
        $configFalse[] = $criteria;
        $criteria->limitQuantity = 5; $criteria->limitWeight = 23; $criteria->limitBalance = 356.60;
        $configFalse[] = $criteria;
        $criteria->limitQuantity = 6; $criteria->limitWeight = 24; $criteria->limitBalance = 356.60;
        $configFalse[] = $criteria;

        foreach($configFalse as $config)
        {
            $checkPurchaseQuantity->setLimit($config->limitQuantity);
            $checkPurchaseWeight->setLimit($config->limitWeight);
            $checkBalance->setLimit($config->limitBalance);

            $check = $verifier1->isAllowed();
            $this->assertFalse($check);
        }
    }
}