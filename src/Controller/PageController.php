<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * 页面控制器，只加载页面
 */
class BaseController extends Controller
{

    public function login(): Response
    {
        return $this->render('user/login.html.twig');
    }
}
