<?php

namespace App\Repository;

use App\Entity\Major;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Major|null find($id, $lockMode = null, $lockVersion = null)
 * @method Major|null findOneBy(array $criteria, array $orderBy = null)
 * @method Major[]    findAll()
 * @method Major[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MajorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Major::class);
    }

    public function insert($data): array
    {
        $entityManager = $this->getEntityManager();
        $majors = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $major = new Major();
            $majors[] = $major;
            $major->setName($data[$i]['name']);
            $major->setCollegeId($data[$i]['collegeId']);
            $entityManager->persist($major);
        }
        $entityManager->flush();
        return $majors;
    }


//    /**
//     * @return Major[] Returns an array of Major objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Major
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
