<?php
namespace App\Application\Handler\Client;

use App\Domain\Client\Client;
use App\Domain\Client\ClientRepositoryInterface;

use Doctrine\ORM\Exception\ORMException;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class CreateClientHandler
{
    private ClientRepositoryInterface $clientRepository;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        UserPasswordHasherInterface $hasher
    )
    {
        $this->clientRepository = $clientRepository;
        $this->hasher = $hasher;
    }

    /**
     * @param array $clientArray
     * @throws Exception|ORMException
     */
    public function handle(array $clientArray): void
    {
        $entityClient = Uuid::v7(); // 018f0326-4186-769a-8a6c-78876c0d76bc
        $client = new Client();
        $client->setId($entityClient);
        $client->setUsername($clientArray['username']);
        $client->setEmail($clientArray['email']);

        $password = $this->hasher->hashPassword($client, $clientArray['password']);
        $client->setPassword($password);

        try {
            $this->clientRepository->save($client);
        } catch (Exception $exception) {
            throw new Exception ('User can not be saved, probably username or email already taken.');
        }
    }
}
