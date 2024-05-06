<?php
namespace App\Domain\BankAccount;

use App\Domain\Client\Client;
use Exception;
use Symfony\Component\Uid\Uuid;

class FirmBankAccount implements BankAccountRepositoryInterface, FirmBankAccountRepositoryInterface
{
    private Uuid $id;
    public string $name;
    public Client $client;
    protected float $balance = 0.000;

    public function __construct()
    {
    }
    public function getId(): Uuid
    {
        return $this->id;
    }
    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getBalance(): float
    {
        return $this->balance;
    }
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }
    public function getClient(): Client
    {
        return $this->client;
    }
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function MakeDeposit(float $amount): void
    {
        echo "Bank account deposit: ".$amount.PHP_EOL;

        if($amount < 0) {
            throw new Exception("Amount must be positive number".PHP_EOL); }

        $this->balance += $amount;
    }

    public function MakeWithdrawal(float $amount): void
    {
        echo "Make withdrawal from company bank account: ".PHP_EOL;
        echo " Bank account withdrawal: ".$amount.PHP_EOL;

        if($amount < 0) {
            throw new Exception("Amount must be positive number".PHP_EOL); }

        $this->balance -= $amount;
    }

    public function TakeLoan(float $amount): void
    {
        echo "Business account take loan".PHP_EOL;
    }
}
