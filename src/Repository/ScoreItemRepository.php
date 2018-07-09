<?php

namespace App\Repository;

use App\Entity\ScoreItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ScoreItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScoreItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScoreItem[]    findAll()
 * @method ScoreItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ScoreItem::class);
    }

//    /**
//     * @return ScoreItem[] Returns an array of ScoreItem objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScoreItem
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
