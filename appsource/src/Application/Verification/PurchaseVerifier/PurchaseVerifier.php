<?php
namespace App\Application\Verification\PurchaseVerifier;


interface PurchaseVerifier
{
    public function verificationQuantity(VerificationPurchaseQuantity $element): bool;

    public function verificationWeight(VerificationPurchaseWeight $element): bool;
    public function verificationBalance(VerificationBalanceForPurchase $element): bool;
}
