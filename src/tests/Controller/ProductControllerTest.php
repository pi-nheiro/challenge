<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductControllerTest extends WebTestCase
{
    private const API_PRODUCT_URL = '/api/products';
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    private function createProductViaApi(array $productData): array{
        $this->client->request(
            'POST',
            self::API_PRODUCT_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($productData)
        );

        $this->assertResponseStatusCodeSame(201);
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    public function testIndexReturnsPaginatedProducts(): void
    {
        $this->client->request('GET', self::API_PRODUCT_URL . '?page=1');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($data);

        // $this->assertGreaterThanOrEqual(0, count($data));
        $this->assertCount(10, $data);
        if(!empty($data)){
            $this->assertArrayHasKey('id', $data[0]);
            $this->assertArrayHasKey('name', $data[0]);
            $this->assertArrayHasKey('price', $data[0]);
        }
    }

    public function testCreateProduct(): void
    {
        $newProductData = [
            'name' => 'Novo produto criado em teste.',
            'price' => 99.99,
            'description' => 'no método testCreateProduct'
        ];

        $responseData = $this->createProductViaApi($newProductData);


        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($newProductData['name'], $responseData['name']);
        $this->assertEquals($newProductData['price'], $responseData['price']);
        $this->assertEquals($newProductData['description'], $responseData['description']);
    }

    public function testShowProduct(): void
    {
        $newProductData = [
            'name' => 'Produto criado para teste',
            'price' => 99.99,
            'description' => 'Produto criado para testar endpoint de visualização'
        ];

        $responseData = $this->createProductViaApi($newProductData);
        $productId = $responseData['id'];

        $this->client->request('GET', self::API_PRODUCT_URL . "/{$productId}");

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $showData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($newProductData['name'], $showData['name']);
        $this->assertEquals($newProductData['price'], $showData['price']);
        $this->assertEquals($newProductData['description'], $showData['description']);
    }
}
