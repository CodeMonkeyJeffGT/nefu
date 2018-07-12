<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ScoreSortService;

class ScoreController extends Controller
{
    public function index(ScoreSortService $scoreSorter): JsonResponse
    {
        $nefuer = $this->getNefuer();
        if (false == $nefuer) {
            return $this->toUrl('/auto?ope=page-score');
        }
        $score = $scoreSorter->getScore($nefuer->getAccount(), $nefuer);
        $this->session->set('nefuer_cookie', ($nefuer->getCookie()));
        return $this->success($score);
    }

}
