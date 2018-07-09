<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function listScores($account)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT `r`.`id` `id`,
            `o`.`status` `os`, `o`.`in_date` `check_date`, `o`.`days` `bookDays`,
            `b`.`status` `bs`, `b`.`book_date` `book_date`, `b`.`days` `checkDays`
            FROM `room` `r`
            LEFT JOIN `occupancy` `o` ON `o`.`r_id` = `r`.`id`
            LEFT JOIN `booking` `b` ON `b`.`r_id` = `r`.`id`
            WHERE `r`.`id` = :id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(
            'id' => $roomId,
        ));
        return $stmt->fetchAll();
    }

//    /**
//     * @return Lesson[] Returns an array of Lesson objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lesson
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
