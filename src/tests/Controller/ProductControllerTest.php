<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductControllerTest extends WebTestCase
{
    public function testIndexReturnsPaginatedProducts(): void{
        $client = static::createClient();

        $client->request('GET', '/api/products?page=1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);

        $this->assertCount(10, $data);
        $this->assertArrayHasKey('id', $data[0]);
    }
}
