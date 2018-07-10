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
        if (isset($scoreAll['code'])) {
            $scoreAll = array();
        }
        if (isset($scoreItem['code'])) {
            $scoreItem = array();
        }
        $this->session->set('nefuer_cookie', ($nefuer->getCookie()));
        $scoreAllDb = $this->getDoctrine()->getRepository(ScoreAll::class);
        $scoreItemDb = $this->getDoctrine()->getRepository(ScoreItem::class);
        $oldAll = $scoreAllDb->listScores($nefuer->getAccount());
        $oldItem = $scoreItemDb->listScores($nefuer->getAccount());
        return $this->updateScore($scoreAll, $scoreItem, $oldAll, $oldItem, $nefuer->getAccount());
    }

    private function updateScore($scoreAll, $scoreItem, $oldAll, $oldItem, $account): array
    {
        $flag = true;
        if (count($oldAll) === 0 && count($oldAll) === 0) {
            $flag = false;
        }
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
                        'num' => $oldAll[$key]['num'],
                    );
                }
            } else {
                $lessonNeed[$score['code']] = array(
                    'name' => $score['name'],
                    'num' => $score['num'],
                );

                $newAll[] = array(
                    'account' => $account,
                    'code' => $score['code'],
                    'name' => $score['name'],
                    'score' => $score['score'],
                    'num' => $score['num'],
                    'term' => $score['term'],
                );
                $update['all'][] = array(
                    'name' => $score['name'],
                    'score' => $score['score'],
                    'num' => $score['num'],
                );
                $oldAll = array_merge(array(
                    array(
                        'score' => $score['score'],
                        'code' => $score['code'],
                        'name' => $score['name'],
                        'term' => $score['term'],
                    ),
                ), $oldAll);
            }
        }

        //阶段更改结构
        $scoreItemList = array();
        foreach ($scoreItem as $score) {
            foreach ($score['item'] as $key => $item) {
                $scoreItemList[] = array(
                    'score' => $item,
                    'code' => $score['code'],
                    'name' => $score['name'],
                    'num' => $score['num'],
                    'term' => $score['term'],
                    'type' => 'itm' . ($key + 1),
                );
            }
            if ($score['normal'] != '') {
                $scoreItemList[] = array(
                    'score' => $score['normal'],
                    'code' => $score['code'],
                    'name' => $score['name'],
                    'num' => $score['num'],
                    'term' => $score['term'],
                    'type' => 'nml',
                );
            }
            if ($score['mid'] != '') {
                $scoreItemList[] = array(
                    'score' => $score['mid'],
                    'code' => $score['code'],
                    'name' => $score['name'],
                    'num' => $score['num'],
                    'term' => $score['term'],
                    'type' => 'mid',
                );
            }
            if ($score['fin'] != '') {
                $scoreItemList[] = array(
                    'score' => $score['fin'],
                    'code' => $score['code'],
                    'name' => $score['name'],
                    'num' => $score['num'],
                    'term' => $score['term'],
                    'type' => 'fin',
                );
            }
        }
        $scoreItem = $scoreItemList;
        unset($scoreItemList);

        //阶段判断更新
        foreach ($scoreItem as $score) {
            if ($keys = $this->existScoreItem($score, $oldItem) !== false) {
                if ($score['score'] != $oldItem[$key]['score']) {
                    $oldItem[$key]['score'] = $score['score'];
                    $updateItem[] = array(
                        'id' => $oldItem[$key]['id'],
                        'score' => $oldItem[$key]['score'],
                    );
                    $update['item'][] = array(
                        'name' => $oldItem[$key]['name'],
                        'score' => $oldItem[$key]['score'],
                        'type' => $oldItem[$key]['type'],
                    );
                }
            } else {
                $lessonNeed[$score['code']] = array(
                    'name' => $score['name'],
                    'num' => $score['num'],
                );

                $newItem[] = array(
                    'account' => $account,
                    'code' => $score['code'],
                    'name' => $score['name'],
                    'score' => $score['score'],
                    'term' => $score['term'],
                    'type' => $score['type'],
                    'num' => $score['num'],
                );
                $update['item'][] = array(
                    'name' => $score['name'],
                    'score' => $score['score'],
                    'type' => $score['type'],
                );
                $oldItem = array_merge(array(
                    array(
                        'score' => $score['score'],
                        'code' => $score['code'],
                        'name' => $score['name'],
                        'term' => $score['term'],
                        'type' => $score['type'],
                    ),
                ), $oldItem);
            }
        }

        //课程获取id
        if (count($lessonNeed) > 0) {
            $lessonNeed = $lessonDb->getIds($lessonNeed);
        }

        // //总成绩插入更新
        // if (count($updateAll) > 0) {
        //     $scoreAllDb->update($updateAll);
        // }
        // //总成绩插入新增
        // if (count($insertAll) > 0) {
        //     $scoreAllDb->insert($insertAll);
        // }

        //阶段插入更新
        
        //阶段插入新增
        

        $terms = array();
        //总成绩排序
        foreach ($oldAll as $score) {
            if ( ! isset($terms[$score['term']])) {
                $terms[$score['term']] = array(
                    'all' => array(),
                    'item' => array(),
                    'scoreT' => 0,
                    'numT' => 0,
                    'scoreF' => 0,
                    'numF' => 0,
                );
            }
            $terms[$score['term']]['all'][] = $score;
        }
        //阶段排序
        foreach ($oldItem as $score) {
            if ( ! isset($terms[$score['term']])) {
                $terms[$score['term']] = array(
                    'all' => array(),
                    'item' => array(),
                    'scoreT' => 0,
                    'numT' => 0,
                    'scoreF' => 0,
                    'numF' => 0,
                );
            }
        }

        if ( ! $flag) {
            $update = array(
                'all' => array(),
                'item' => array(),
            );
        }
        return array(
            'avg' => $avg,
            'all' => $oldAll,
            'item' => $oldItem,
            'update' => $update,
        );
    }

    private function existScore($score, $arr): bool
    {
        foreach ($arr as $key => $value) {
            if ($score['code'] === $value['code']) {
                return $key;
            }
        }
        return false;
    }

    private function existScoreItem($score, $arr): bool
    {
        foreach ($arr as $key => $value) {
            if ($score['code'] === $value['code'] && $score['type'] === $value['type']) {
                return $key;
            }
        }
        return false;
    }
}
