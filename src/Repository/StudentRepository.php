<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function insert($data): array
    {
        $entityManager = $this->getEntityManager();
        $students = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $student = new Student();
            $students[] = $student;
            $student->setAccount($data[$i]['account']);
            $student->setPassword($data[$i]['password']);
            $student->setOpenid($data[$i]['openid']);
            $student->setCreated(new \DateTime('now', new \DateTimeZone('PRC')));
            $student->setMajorId($data[$i]['majorId']);
            $student->setGrade($data[$i]['grade']);
            $student->setSex($data[$i]['sex']);
            $student->setNickname($data[$i]['account']);
            $entityManager->persist($student);
        }
        $entityManager->flush();
        return $students;
    }

//    /**
//     * @return Student[] Returns an array of Student objects
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
    public function findOneBySomeField($value): ?Student
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
