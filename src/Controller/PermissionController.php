<?php

namespace App\Controller;
use App\Entity\Permission;

use Symfony\Component\HttpFoundation\JsonResponse;

class PermissionController extends Controller
{
    public function getPermit()
    {
        if (false == $this->getNefuer()) {
            return $this->toUrl('/auto');
        }
        $account = $this->session->get('nefuer_account');
        $permissionDb = $this->getDoctrine()->getRepository(Permission::class);
        $permits = $permissionDb->listPermits($account);
        $permit = array();
        for ($i = 0, $len = count($permits); $i < $len; $i++)
        {
            $permit[$permits[$i]['name']] = $permits[$i]['permit'];
        }
        return $this->success($permit);
    }

    public function switchScore()
    {
        return $this->switchPermit('成绩');
    }

    public function switchItem()
    {
        return $this->switchPermit('阶段成绩');
    }

    private function switchPermit($name)
    {
        if (false == $this->getNefuer()) {
            return $this->toUrl('/auto');
        }
        $permissionDb = $this->getDoctrine()->getRepository(Permission::class);
        $account = $this->session->get('nefuer_account');
        $permit  = $this->request->request->get('permit') == 'true';
        $permissionDb->switchPermit($account, $name, $permit);
        return $this->success();
    }
}
