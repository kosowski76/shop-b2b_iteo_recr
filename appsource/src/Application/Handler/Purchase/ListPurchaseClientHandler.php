<?php
namespace App\Application\Handler\Purchase;

use App\Domain\Client\ClientRepositoryInterface;
use App\Domain\Purchase\PurchaseRepositoryInterface;
use App\Domain\PurchaseItem\PurchaseItemRepositoryInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;

class ListPurchaseClientHandler
{
    protected ClientRepositoryInterface $clientRepository;
    protected PurchaseRepositoryInterface $purchaseRepository;
    protected PurchaseItemRepositoryInterface $purchaseItemRepository;
    protected Security $security;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        PurchaseRepositoryInterface $purchaseRepository,
        PurchaseItemRepositoryInterface $purchaseItemRepository,
        Security $security
    ){
        $this->clientRepository = $clientRepository;
        $this->purchaseRepository = $purchaseRepository;
        $this->purchaseItemRepository = $purchaseItemRepository;
        $this->security = $security;
    }

    /**
     * @return array
     * @throws Exception
     **/
    public function handle(): array
    {
        $client = $this->clientRepository->findOneBy(
            ['username' => $this->security->getUser()->getUserIdentifier()]);

        $purchaseList = $this->purchaseRepository->findBy(
            ['client' => $client]);

        return $purchaseList;
    }
}
