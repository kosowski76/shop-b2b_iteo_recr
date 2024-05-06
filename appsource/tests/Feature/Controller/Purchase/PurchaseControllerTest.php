<?php
namespace App\Tests\Feature\Controller\Purchase;

use App\Infrastructure\Doctrine\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PurchaseControllerTest extends WebTestCase
{
    public function testCreatePurchaseErrorFromApi_ShouldReturnUnauthorized()
    {
        $client = static::createClient([],
            [
                'HTTP_HOST' => '127.0.0.1',
            ]);
        $client->request(
            'POST',
            '/api/order', [], [], [],
            '[{"name":"coal", "weight": 1.3}]'
        );
        $expectResult = '{"code":401,"message":"JWT Token not found"}';
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectResult, $client->getResponse()->getContent());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreatePurchaseInvalidJsonRequest_ShouldReturnBadRequest()
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

        $client->request(
            'POST',
            '/api/order', [], [], [],
            '``'
        );
        $expectResult = '{"message":"Invalid json Request body","status_code":400}';
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectResult, $client->getResponse()->getContent());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreatePurchaseValidOrder_ShouldReturnCreated()
    {
        $client = static::createClient([],
            [
                'HTTP_HOST' => '127.0.0.1',
            ]);
        $userRepository = static::getContainer()->get(ClientRepository::class);
        $testUser = $userRepository->findOneByEmail('client@test.com');
        $client->loginUser($testUser);

        $client->request(
            'POST',
            '/api/order', [], [], [],
            '[{"productId":"018F231E-BC45-75F4-8CEE-3A94A1866619","quantity":5},'.
            '{"productId":"018F231E-BC45-75F4-8CEE-3A94A28C8C19","quantity":6},'.
            '{"productId":"018F231E-BC45-75F4-8CEE-3A94A3595D4C","quantity":8}]'
        );

        $expectResult = '{"message":"Order created","status_code":201}';
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectResult, $client->getResponse()->getContent());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreatePurchaseInvalidOrderByCriteria_ShouldReturnBadRequest()
    {
        $client = static::createClient([],
            [
                'HTTP_HOST' => '127.0.0.1',
            ]);
        $userRepository = static::getContainer()->get(ClientRepository::class);
        $testUser = $userRepository->findOneByEmail('client@test.com');
        $client->loginUser($testUser);

        $client->request(
            'POST',
            '/api/order', [], [], [],
            '[{"productId":"018F231E-BC45-75F4-8CEE-3A94A1866619","quantity":4},'.
            '{"productId":"018F231E-BC45-75F4-8CEE-3A94A28C8C19","quantity":1},'.
            '{"productId":"018F231E-BC45-75F4-8CEE-3A94A3595D4C","quantity":3}]'
        );

        $expectResult = '{"message":"Order can not allowed to created","status_code":400}';
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $this->assertEquals($expectResult, $client->getResponse()->getContent());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreatePurchaseErrorFromApi_ShouldReturnBadRequest()
    {
        $client = static::createClient([],
            [
                'HTTP_HOST' => '127.0.0.1',
            ]);
        $userRepository = static::getContainer()->get(ClientRepository::class);
        $testUser = $userRepository->findOneByEmail('client@test.com');
        $client->loginUser($testUser);

        $client->request(
            'POST',
            '/api/order', [], [], [],
            '{"name": "testing"}'
        );

        $request_body = $client->getRequest()->getContent();

        $expectResult = '{"message":"Warning: Undefined array key","status_code":500}';
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
    //    $this->assertEquals($expectResult, $client->getResponse()->getContent());
    //    $this->assertJson($client->getResponse()->getContent());
    }
}
