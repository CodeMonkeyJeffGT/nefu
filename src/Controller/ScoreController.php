<?php

namespace App\Controller;

use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\ScoreAll;
use App\Entity\ScoreItem;
use App\Entity\Lesson;

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
        return $this->updateScore($scoreAll, $scoreItem, $oldAll, $oldItem, $nefuer->getAccount());
    }

    private function updateScore($scoreAll, $scoreItem, $oldAll, $oldItem, $account): array
    {
        $avg = array();
        $update = array(
            'all' => array(),
            'item' => array(),
        );

        $lessonNeed = array();
        $updateAll = array();
        $newAll = array();
        $updateItem = array();
        $newItem = array();

        $lessonDb = $this->getDoctrine()->getRepository(Lesson::class);
        $scoreAllDb = $this->getDoctrine()->getRepository(ScoreAll::class);
        $scoreItemDb = $this->getDoctrine()->getRepository(ScoreItem::class);
        //总成绩判断更新
        foreach ($scoreAll['score'] as $score) {
            if ($score['status'] != 'done') {
                continue;
            }
            if ($key = $this->existScore($score, $oldAll) !== false) {
                if ($score['score'] != $oldAll[$key]['score']) {
                    $oldAll[$key]['score'] = $score['score'];
                    $updateAll[] = array(
                        'id' => $oldAll[$key]['id'],
                        'score' => $oldAll[$key]['score'],
                    );
                    $update['all'][] = array(
                        'name' => $oldAll[$key]['name'],
                        'score' => $oldAll[$key]['score'],
                    );
                }
            } else {
                $lessonNeed['code'] = $score['name'];

                $newAll[] = array(
                    'account' => $account,
                    'code' => $score['code'],
                    'name' => $score['name'],
                    'score' => $score['score'],
                    'term' => $score['term'],
                );
                $update['all'][] = array(
                    'name' => $score['name'],
                    'score' => $score['score'],
                );
                $oldAll = array(
                    array(
                        'score' => $score['score'],
                        'name' => $score['name'],
                        'term' => $score['term'],
                    ),
                ) + $oldAll;
            }
        }

        return $oldAll;
        //阶段判断更新
        foreach ($scoreItem as $score) {
        }

        //课程获取id
        if (count($lessonNeed) > 0) {
            $lessonNeed = $lessonDb->getIds($lessonNeed);
        }

        //总成绩插入更新
        if (count($updateAll) > 0) {
            $scoreAllDb->update($updateAll);
        }
        //总成绩插入新增
        if (count($insertAll) > 0) {
            $scoreAllDb->insert($insertAll);
        }

        //阶段插入更新
        
        //阶段插入新增
        

        //总成绩排序
        foreach ($oldAll as $score) {
            
        }
        //阶段排序
        foreach ($oldItem as $score) {
            
        }
        return array(
            'avg' => $avg,
            'all' => $scoreAll,
            'item' => $scoreItem,
            'update' => $update,
        );
    }

    private function existScore($score, $arr): bool
    {
        foreach ($arr as $key => $value) {
            var_dump($arr);
            var_dump($score);
            var_dump($value);die;
            if ($score['code'] === $value['code']) {
                return $key;
            }
        }
        return false;
    }

    private function existScoreChange($score, $arr): bool
    {

    }
}
