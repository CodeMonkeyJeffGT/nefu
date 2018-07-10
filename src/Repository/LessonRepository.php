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

    public function insert($data): array
    {
        $entityManager = $this->getEntityManager();
        $lessons = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $lesson = new Lesson();
            $lessons[] = $lesson;
            $lesson->setCode($data[$i]['code']);
            $lesson->setName($data[$i]['name']);
            $entityManager->persist($lesson);
        }
        $entityManager->flush();
        return $lessons;
    }

    public function getIds($lessons): array
    {
        $codes = array_keys($lessons);
        $sql = 'SELECT `l`.`id`, `l`.`code`
            FROM `lesson` `l`
            WHERE `l`.`code` IN ("' . implode('", "', $codes) .  '")
        ';
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute(array());
        $ids = $stmt->fetchAll();
        $lessonsGot = array();
        foreach ($ids as $lesson)  {
            $lessonsGot[$lesson['code']] = $lesson['id'];
            unset($lessons[$lesson['code']]);
        }
        $ids = array();
        foreach ($lessons as $key => $value) {
            $ids[] = array(
                'code' => $key,
                'name' => $value['name'],
            );
        }
        $ids = $this->insert($ids);
        foreach ($ids as $lesson) {
            $lessonsGot[$lesson->getCode()] = $lesson->getId();
        };
        return $lessonsGot;
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
