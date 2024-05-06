<?php
namespace App\Tests\Unit\Infrastructure\Doctrine;

use App\Domain\BankAccount\FirmBankAccount;
use App\Domain\Client\Client;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class getBalanceRepositoryUnitTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testGetBalance_MakeDeposit_MakeWithdrawalReturn_float(): void
    {
        $entityClient = Uuid::v7(); // 018f0326-4186-769a-8a6c-78876c0d76bc

        $client = new Client();
        $client->setId($entityClient);
        $client->setEmail('client@test.com');
        $client->setUsername('tester');
        $client->setPassword('123123');
        $client->setCreatedAt(new DateTime('now'));

        $balance = 0.000;
        $firmBankAccount = new FirmBankAccount("FirmAccount1", $client, $balance);
        $result = $firmBankAccount->getBalance();
        $this->assertEquals($balance, $result);
        $this->assertNotEquals(1.01, $result);
        $this->assertNotEquals(0.01, $result);
        $this->assertNotEquals(-1.01, $result);

        $deposit = 125.98;
        $firmBankAccount->MakeDeposit($deposit);
        $result = $firmBankAccount->getBalance();
        $this->assertEquals($deposit, $result);
        $this->assertNotEquals(0.00, $result);
        $this->assertNotEquals(2.01, $result);
        $this->assertNotEquals(126.00, $result);
        $this->assertNotEquals(-125.01, $result);

        $makeWithdrawal = 50.00;
        $firmBankAccount->MakeWithdrawal($makeWithdrawal);
        $result = $firmBankAccount->getBalance();
        $this->assertEquals($deposit - $makeWithdrawal, $result);
        $this->assertNotEquals(75.01, $result);
        $this->assertNotEquals(50.00, $result);
        $this->assertNotEquals(-76.98, $result);

        //$this->assertTrue(true);
    }
    public function testGivenWrongDepositReturn_Exception(): void
    {

        $this->expectException(Exception::class);

        $entityClient = Uuid::v7(); // 018f0326-4186-769a-8a6c-78876c0d76bc

        $client = new Client();
        $client->setId($entityClient);
        $client->setEmail('client@test.com');
        $client->setUsername('tester');
        $client->setPassword('123123');
        $client->setCreatedAt(new DateTime('now'));

        $balance = 0.000;
        $firmBankAccount = new FirmBankAccount("FirmAccount1", $client);
        $deposit = -1;
        $firmBankAccount->MakeDeposit($deposit);
        $result = $firmBankAccount->getBalance();
    }
    public function testGivenWrongMakeWithdrawalReturn_Exception(): void
    {
        $this->expectException(Exception::class);

        $entityClient = Uuid::v7(); // 018f0326-4186-769a-8a6c-78876c0d76bc

        $client = new Client();
        $client->setId($entityClient);
        $client->setEmail('client@test.com');
        $client->setUsername('tester');
        $client->setPassword('123123');
        $client->setCreatedAt(new DateTime('now'));

        $balance = 0.000;
        $firmBankAccount = new FirmBankAccount("FirmAccount1", $client);
        $makeWithdrawal = -1;
        $firmBankAccount->MakeWithdrawal($makeWithdrawal);
        $result = $firmBankAccount->getBalance();
    }
}