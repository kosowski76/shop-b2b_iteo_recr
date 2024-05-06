<?php
namespace App\Tests\Feature\Infrastucture\Doctrine;

use App\Domain\Product\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByName(): void
    {
        $product = $this->entityManager
            ->getRepository(Product::class)
            ->findOneBy(['name' => 'Coal']);

        $this->assertSame(1107.00, $product->getSellingPrice());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        //$this->entityManager = null;
    }

}