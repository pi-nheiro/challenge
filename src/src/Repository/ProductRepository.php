<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findPaginated(int $currentPage = 1, int $limit = 10){
        $query = $this->createQueryBuilder('e')
        ->orderBy('e.id', 'ASC')
        ->getQuery()
        ->setFirstResult(($currentPage - 1) * $limit)
        ->setMaxResults($limit);

        return new Paginator($query, true);
    }

    public function findTopRated(int $limit = 10): array{
        return $this->createQueryBuilder('p')
        ->leftJoin('p.reviews', 'r')
        ->addSelect('AVG(r.rating) AS avgRating')
        ->groupBy('p.id')
        ->having('COUNT(r.id) > 0')
        ->orderBy('avgRating', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
