<?php
namespace App\Tests\Feature\Controller\Client;

use App\Infrastructure\Doctrine\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientControllerTest extends WebTestCase
{
    public function testVisitingWhileLoggedIn(): void
    {
        $client = static::createClient([],
            [
                'HTTP_HOST' => '127.0.0.1',
            ]);
        $userRepository = static::getContainer()->get(ClientRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('client@test.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // test e.g. the profile page
        $client->request('GET', '/api/client');
        $this->assertResponseIsSuccessful();
        $this->assertJson('{"username": "client","message": "Hello Client, client"}');
    }
}