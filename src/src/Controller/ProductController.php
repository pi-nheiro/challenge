<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products')]
final class ProductController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(ProductRepository $repo, Request $request): JsonResponse
    {
        $currentPage = $request->query->getInt('page', 1);
        $limit = 10;

        $paginator = $repo->findPaginated($currentPage, $limit);
        // $products = $repo->findAll();
        return $this->json($paginator, 200, );
    }

    #[Route('', methods:['POST'])]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description'] ?? null);
        $product->setCreatedAt(new \DateTimeImmutable());

        $em->persist($product);
        $em->flush();

        return $this->json($product, 201);
    }

    #[Route('/top-products', name: 'top_products', methods: ['GET'])]
    public function topRated(ProductRepository $repo): JsonResponse
    {
        $topProducts = $repo->findTopRated();

        $formatted = array_map(function ($row) {
            return [
                'id' => $row[0]->getId(),
                'name' => $row[0]->getName(),
                'price' => $row[0]->getPrice(),
                'description' => $row[0]->getDescription(),
                'avgRating' => round($row['avgRating'], 2),
            ];
        }, $topProducts);

        return $this->json($formatted);
    }


    #[Route('/{id}', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        if(!$product){
            return $this->json(['Error' => 'Produto não encontrado']);
        }

        return $this->json($product, 200);
    }

    #[Route('/{id}/edit', methods: ['PUT'])]
    public function update(Request $request, Product $product, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        $product->setName($data['name'] ?? $product->getName());
        $product->setPrice($data['price'] ?? $product->getPrice());
        $product->setDescription($data['description'] ?? $product->getDescription());

        $em->flush();

        return $this->json($product, 200);
    }

    #[Route('/{id}/delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($product);
        $em->flush();

        return $this->json(null, 204);
    }

}