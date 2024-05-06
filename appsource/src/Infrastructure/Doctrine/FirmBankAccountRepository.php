<?php
namespace App\Infrastructure\Doctrine;

use App\Domain\BankAccount\BankAccountRepositoryInterface;
use App\Domain\BankAccount\FirmBankAccount;
use App\Domain\BankAccount\FirmBankAccountRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class FirmBankAccountRepository extends ServiceEntityRepository implements FirmBankAccountRepositoryInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager
    ){
        parent::__construct($registry, FirmBankAccount::class);
        $this->entityManager = $entityManager;
    }
    /**
     * @param FirmBankAccount $firmBankAccount
     */
    public function save(FirmBankAccount $firmBankAccount)
    {
        $this->entityManager->persist($firmBankAccount);
        $this->entityManager->flush();
    }

}