<?php

namespace App\Controller;

use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ScoreController extends Controller
{
    public function index()
    {
        $nefuer = $this->getNefuer();
        if (false == $nefuer) {
            return $this->toUrl('/auto');
        }
        $scoreAll = $nefuer->scoreAll();
        $scoreItem = $nefuer->scoreItem();
        $this->setCookie($nefuer->getCookie());

    }
}
