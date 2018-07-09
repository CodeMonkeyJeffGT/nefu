<?php

namespace App\Controller;

use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\ScoreAll;
use App\Entity\ScoreItem;

class ScoreController extends Controller
{
    public function index(): JsonResponse
    {
        $nefuer = $this->getNefuer();
        if (false == $nefuer) {
            return $this->toUrl('/auto');
        }
        return $this->success($this->listScore($nefuer));
    }

    private function listScore($nefuer): array
    {
        $scoreAll = $nefuer->scoreAll();
        $scoreItem = $nefuer->scoreItem();
        $this->session->set('nefuer_cookie', ($nefuer->getCookie()));
        $scoreAllDb = $this->getDoctrine()->getRepository(ScoreAll::class);
        $scoreItemDb = $this->getDoctrine()->getRepository(ScoreItem::class);
        $oldAll = $scoreAllDb->listScores($nefuer->getAccount());
        $oldItem = $scoreItemDb->listScores($nefuer->getAccount());
        return $this->updateScore($scoreAll, $scoreItem, $oldAll, $oldItem);
    }

    private function updateScore($scoreAll, $scoreItem, $oldAll, $oldItem): array
    {
        $avg = array();
        $update = array();
        return array(
            'avg' => $avg,
            'all' => $oldAll,
            'item' => $oldItem,
            'update' => $update,
        );
    }
}
