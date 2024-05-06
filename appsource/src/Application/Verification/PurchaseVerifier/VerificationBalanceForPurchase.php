<?php
namespace App\Application\Verification\PurchaseVerifier;

use App\Domain\BankAccount\BankAccountRepositoryInterface;

class VerificationBalanceForPurchase implements PurchaseVerification
{
    protected float $limit = 0.00;

    public function verification(PurchaseVerifier $verifier): void
    {
        $verifier->verificationBalance($this);
    }
    public function getCheckedQuantity(BankAccountRepositoryInterface $purchase): bool
    {
        if($purchase->getBalance() >= ($this->limit))
        { return true; }

        return false;
    }
    public function setLimit(float|null $limit)
    {
        $this->limit = $limit;
    }

}