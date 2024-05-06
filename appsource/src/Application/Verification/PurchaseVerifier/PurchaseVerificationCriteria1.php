<?php
namespace App\Application\Verification\PurchaseVerifier;

use App\Domain\BankAccount\BankAccountRepositoryInterface;
use App\Domain\Purchase\Purchase;

class PurchaseVerificationCriteria1 implements PurchaseVerifier
{
    protected Purchase $purchase;
    protected BankAccountRepositoryInterface $bankAccount;
    protected VerificationPurchaseQuantity $elementQuantity;
    protected VerificationPurchaseWeight $elementWeight;
    protected VerificationBalanceForPurchase $elementBalance;

    public function __construct(Purchase $purchase, BankAccountRepositoryInterface $bankAccount)
    {
        $this->purchase = $purchase;
        $this->bankAccount = $bankAccount;
    }
    public function verificationQuantity(VerificationPurchaseQuantity $element): bool
    {
        $this->elementQuantity = $element;
        return $element->getCheckedQuantity($this->purchase);
    }
    public function verificationWeight(VerificationPurchaseWeight $element): bool
    {
        $this->elementWeight = $element;
        return $element->getCheckedWeight($this->purchase);
    }
    public function verificationBalance(VerificationBalanceForPurchase $element): bool
    {
        $this->elementBalance = $element;
        return $element->getCheckedQuantity($this->bankAccount);
    }
    public function isAllowed(): bool
    {
        if($this->verificationQuantity($this->elementQuantity) &&
            $this->verificationWeight($this->elementWeight) &&
            $this->verificationBalance($this->elementBalance)
        ) {
            return 1;
        }
        return 0;
    }
}
