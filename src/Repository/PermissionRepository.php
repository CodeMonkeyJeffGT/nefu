<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function insert($data): array
    {
        $entityManager = $this->getEntityManager();
        $permisisions = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $permisision = new Permission();
            $permisisions[] = $permisision;
            $permisision->setName($data[$i]['name']);
            $permisision->setAccount($data[$i]['account']);
            $permisision->setPermit($data[$i]['permit']);
            $entityManager->persist($permisision);
        }
        $entityManager->flush();
        return $permisisions;
    }

    public function listPushes(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT `p`.`name` `name`, `p`.`account` `account`, `s`.`password` `password`, `s`.`openid`
            FROM `permission` `p`
            LEFT JOIN `student` `s`
            ON `p`.`account` = `s`.`account`
            WHERE `p`.`name` IN ("成绩", "阶段成绩", "考试")
            AND `p`.`permit` = 1
            AND `s`.`openid` <> ""
            ORDER BY `p`.`name`
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

//    /**
//     * @return Permission[] Returns an array of Permission objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Permission
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
