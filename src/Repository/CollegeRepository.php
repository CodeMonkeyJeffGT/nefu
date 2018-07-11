<?php

namespace App\Repository;

use App\Entity\College;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method College|null find($id, $lockMode = null, $lockVersion = null)
 * @method College|null findOneBy(array $criteria, array $orderBy = null)
 * @method College[]    findAll()
 * @method College[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollegeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, College::class);
    }

    public function insert($data): array
    {
        $entityManager = $this->getEntityManager();
        $colleges = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $college = new College();
            $colleges[] = $college;
            $college->setName($data[$i]['name']);
            $entityManager->persist($college);
        }
        $entityManager->flush();
        return $colleges;
    }

    public function getId($name): int
    {
        $college = $this->createQueryBuilder('c')
            ->andWhere('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if (is_null($college)) {
            $college = $this->insert(array(array(
                'name' => $name,
            )))[0];
        }
        return $college->getId();
    }

//    /**
//     * @return College[] Returns an array of College objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?College
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
