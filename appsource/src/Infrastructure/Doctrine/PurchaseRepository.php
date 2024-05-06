<?php
namespace App\Infrastructure\Doctrine;

use App\Domain\Purchase\Purchase;
use App\Domain\Purchase\PurchaseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseRepository extends ServiceEntityRepository implements PurchaseRepositoryInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Purchase::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Purchase $purchase
     */
    public function save(Purchase $purchase): void
    {
        $this->entityManager->persist($purchase);
        $this->entityManager->flush();
    }
}
