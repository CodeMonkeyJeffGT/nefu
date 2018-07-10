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

    public function insert($data): array
    {
        $entityManager = $this->getEntityManager();
        $scoreItems = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $scoreItem = new ScoreItem();
            $scoreItems[] = $scoreItem;
            $scoreItem->setAccount($data[$i]['account']);
            $scoreItem->setLessonId($data[$i]['lessonId']);
            $scoreItem->setScore($data[$i]['score']);
            $scoreItem->setTerm($data[$i]['term']);
            $scoreItem->setType($data[$i]['type']);
            $entityManager->persist($scoreItem);
        }
        $entityManager->flush();
        return $scoreItems;
    }

    public function listScores($account)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT `s`.`id` `id`, `score` `score`, `l`.`code` `code`, `l`.`name` `name`, `s`.`term` `term`, `s`.`type` `type`, `s`.`lesson_id` `lesson_id`
            FROM `score_item` `s`
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
