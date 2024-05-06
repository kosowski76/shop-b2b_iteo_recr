<?php
namespace App\Application\Verification\PurchaseVerifier;

interface PurchaseVerification
{
    public function verification(PurchaseVerifier $verifier): void;
}