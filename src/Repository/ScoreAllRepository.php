<?php

namespace App\Repository;

use App\Entity\ScoreAll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ScoreAll|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScoreAll|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScoreAll[]    findAll()
 * @method ScoreAll[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreAllRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ScoreAll::class);
    }

    public function listScores($account)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT `score` `score`, `l`.`name` `lesson`, `s`.`term` `term`
            FROM `score_all` `s`
            LEFT JOIN `lesson` `l`
            ON `l`.`id` = `s`.`lesson_id`
            WHERE `account` = :account
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(
            'account' => $account,
        ));
        return $stmt->fetchAll();
    }

//    /**
//     * @return ScoreAll[] Returns an array of ScoreAll objects
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
    public function findOneBySomeField($value): ?ScoreAll
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
