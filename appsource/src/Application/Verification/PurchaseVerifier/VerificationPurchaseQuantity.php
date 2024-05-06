<?php
namespace App\Application\Verification\PurchaseVerifier;

use App\Application\Verification\PurchaseVerifier\PurchaseVerification;
use App\Application\Verification\PurchaseVerifier\PurchaseVerifier;
use App\Domain\Purchase\Purchase;

class VerificationPurchaseQuantity implements PurchaseVerification
{
    protected float $limit = 0.00;

    public function verification(PurchaseVerifier $verifier): void
    {
        $verifier->verificationQuantity($this);
    }
    public function getCheckedQuantity(Purchase $purchase): bool
    {
        if($purchase->getItemCount() >= ($this->limit))
            { return true; }

        return false;
    }
    public function setLimit(float|null $limit)
    {
        $this->limit = $limit;
    }
}
