<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * 页面控制器，只加载页面
 */
class PageController extends Controller
{
    public function sign(): Response
    {
        return $this->render('page/sign.html');
    }

    public function score(): Response
    {
        return $this->render('page/score.html');
    }

    public function lesson(): Response
    {
        return $this->render('page/lesson.html');
    }

    public function oauth(): Response
    {
        return $this->render('page/oauth.html');
    }

    public function auto(): Response
    {
        return $this->render('page/auto.html');
    }

    public function permit(): Response
    {
        return $this->render('page/permit.html');
    }
}
