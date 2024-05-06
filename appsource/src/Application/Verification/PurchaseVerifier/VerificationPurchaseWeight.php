<?php
namespace App\Application\Verification\PurchaseVerifier;

use App\Domain\Purchase\Purchase;

class VerificationPurchaseWeight implements PurchaseVerification
{
    protected ?float $limit = 0.00;

    public function verification(PurchaseVerifier $verifier): void
    {
        $verifier->verificationWeight($this);
    }
    public function getCheckedWeight(Purchase $purchase): bool
    {
        if($purchase->getWeightTotal() <= ($this->limit))
        { return true; }

        return false;
    }
    public function setLimit(float|null $limit)
    {
        $this->limit = $limit;
    }
}
