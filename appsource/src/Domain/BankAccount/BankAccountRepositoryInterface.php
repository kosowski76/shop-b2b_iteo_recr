<?php
namespace App\Domain\BankAccount;

interface BankAccountRepositoryInterface
{
    public function MakeDeposit(float $amount): void;
    public function MakeWithdrawal(float $amount): void;
}