<?php
namespace App\Infrastructure\Doctrine;

use App\Domain\Purchase\PurchaseItem;
use App\Domain\PurchaseItem\PurchaseItemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PurchaseItemRepository extends ServiceEntityRepository implements PurchaseItemRepositoryInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, PurchaseItem::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param PurchaseItem $purchaseItem
     */
    public function save(PurchaseItem $purchaseItem): void
    {
        $this->entityManager->persist($purchaseItem);
        $this->entityManager->flush();
    }
}
