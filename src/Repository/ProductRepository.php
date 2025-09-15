<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    public function findFiltered(?string $search, ?string $category, ?float $minPrice, ?float $maxPrice): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if ($search) {
            $qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($category) {
            $qb->andWhere('p.status = :category')
                ->setParameter('category', $category);
        }

        if ($minPrice !== null) {
            $qb->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice !== null) {
            $qb->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }

        $qb->orderBy('p.createdAt', 'DESC');

        return $qb;
    }
//    public function findByFilters(array $filters = [])
//    {
//        $qb = $this->createQueryBuilder('p')
//            ->where('p.status = 1');
//
//        if (!empty($filters['search'])) {
//            $qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
//                ->setParameter('search', '%' . $filters['search'] . '%');
//        }
//
////        if (!empty($filters['categories'])) {
////            $qb->join('p.categories', 'c')
////                ->andWhere('c.id IN (:categories)')
////                ->setParameter('categories', $filters['categories']);
////        }
//
//        if (!empty($filters['price_max'])) {
//            $qb->andWhere('p.price <= :price_max')
//                ->setParameter('price_max', $filters['price_max']);
//        }
//
//        // Ajouter le tri
//        if (!empty($filters['sort'])) {
//            switch ($filters['sort']) {
//                case 'price_asc':
//                    $qb->orderBy('p.price', 'ASC');
//                    break;
//                case 'price_desc':
//                    $qb->orderBy('p.price', 'DESC');
//                    break;
//                case 'name_asc':
//                    $qb->orderBy('p.name', 'ASC');
//                    break;
//                case 'name_desc':
//                    $qb->orderBy('p.name', 'DESC');
//                    break;
//                default:
//                    $qb->orderBy('p.createdAt', 'DESC');
//                    break;
//            }
//        } else {
//            $qb->orderBy('p.createdAt', 'DESC');
//        }
//
//        return $qb->getQuery();
//    }


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
