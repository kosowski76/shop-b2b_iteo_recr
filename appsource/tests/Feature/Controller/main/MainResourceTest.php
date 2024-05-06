<?php
namespace App\Tests\Feature\Controller\main;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainResourceTest extends WebTestCase
{
    public function testManiDashboard(): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient([],
        [
            'HTTP_HOST' => '127.0.0.1',
        ]);

        // Request a specific page
        $crawler = $client->request('GET', '/');

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello, Client dashboard.');
    }

}