<?php
namespace App\Service;
use App\Entity\Permission;


class ListPushesService
{
    private $entityManager;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function list()
    {
        $permissionDb = $this->entityManager->getRepository(Permission::class);
        $list = $permissionDb->listPushes();
        return $list;
    }
}