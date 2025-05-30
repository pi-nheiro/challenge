<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/products')]
final class ReviewController extends AbstractController
{

    #[Route('/{id}/reviews', name: 'create_review', methods: ['POST'])]
    public function create(int $id, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        
        $product = $em->getRepository(Product::class)->find($id);
        if (!$product){
            return $this->json(['Error' => 'Product not found!'], 404);
        }

        $review = new Review();
        $review->setRating($data['rating']);
        $review->setComment($data['comment']);
        $review->setCreatedAt(new DateTimeImmutable());
        $review->setProduct($product);

        $em->persist($review);
        $em->flush();

        return $this->json($review, 201, [], [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function($object){
                return $object->getId();
            }
        ]);
    }

    #[Route('/{id}/reviews', name: 'list_reviews', methods: ['GET'])]
    public function list(int $id, EntityManagerInterface $em, ReviewRepository $repo, Request $request)
    {
        $currentPage = $request->query->getInt('page', 1);
        $limit = 10;

        $product = $em->getRepository(Product::class)->find($id);
        if(!$product){
            return $this->json(['Error' => 'Produto não encontraod!'], 404);
        }

        $paginator = $repo->findPaginatedByProduct($id, $currentPage, $limit);

        // $reviews = $product->getReviews();
        
        return $this->json($paginator, 200);
    }
}
