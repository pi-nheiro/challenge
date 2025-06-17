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

    public function testCreateProduct(): void{
        $client = static::createClient();

        $client->request('POST', '/api/products', [], [], ['CONTENT_TYPE' => 'application/json'],
    json_encode([
        'name' => 'Produto Teste',
        'price' => 123.45,
        'description' => 'Produto criado em teste automatizado.'
    ]));

    $this->assertResponseStatusCodeSame(201);
    
    $responseData = json_decode($client->getResponse()->getContent(), true);

    $this->assertArrayHasKey('id', $responseData);
    $this->assertEquals('Produto Teste', $responseData['name']);
    $this->assertEquals(123.45, $responseData['price']);
    $this->assertEquals('Produto criado em teste automatizado.', $responseData['description']);
    }

    public function testShowProduct(): void{
        $client = static::createClient();

        $client->request('POST', 
        'api/products',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            'name' => 'Produto Show Teste',
            'price' => 99.99,
            'description' => 'Produto criado para teste do método show',
        ])
    );

    $this->assertResponseStatusCodeSame(201);
    
    $data = json_decode($client->getResponse()->getContent(), true);
    $productId = $data['id'];

    //Fazendo teste do método show
    $client->request('GET', "/api/products/{$productId}");

    $this->assertResponseIsSuccessful();
    $this->assertResponseStatusCodeSame(200);
    
    $showData = json_decode($client->getResponse()->getContent(), true);

    $this->assertEquals('Produto Show Teste', $showData['name']);
    $this->assertEquals(99.99, $showData['price']);
    $this->assertEquals('Produto criado para teste do método show', $showData['description']);
    }
}
