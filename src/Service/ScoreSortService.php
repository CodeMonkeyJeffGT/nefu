<?php
namespace App\Service;

use App\Entity\Lesson;
use App\Entity\ScoreAll;
use App\Entity\ScoreItem;

class ScoreSortService
{
    private $entityManager;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getScore($account, $nefuer = null): array
    {
        try {
            $scoreAll = $nefuer->scoreAll();
            $scoreItem = $nefuer->scoreItem();
        } catch (\Exception $e) {
            $scoreAll = array('score' => array());
            $scoreItem = array();
        }
        if (isset($scoreAll['code'])) {
            $scoreAll = array('score' => array());
        }
        if (isset($scoreItem['code'])) {
            $scoreItem = array();
        }

        $lessonDb = $this->entityManager->getRepository(Lesson::class);
        $scoreAllDb = $this->entityManager->getRepository(ScoreAll::class);
        $scoreItemDb = $this->entityManager->getRepository(ScoreItem::class);
        
        $oldAll = $scoreAllDb->listScores($account);
        $oldItem = $scoreItemDb->listScores($account);

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

        //总成绩判断更新
        foreach ($scoreAll['score'] as $score) {
            if ($score['status'] != 'done') {
                continue;
            }
            $key = $this->existScore($score, $oldAll);
            if ($key !== false) {
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
                );

                $newAll[] = array(
                    'account' => $account,
                    'code' => $score['code'],
                    'name' => $score['name'],
                    'score' => $score['score'],
                    'num' => $score['num'],
                    'term' => $score['term'],
                    'type' => $score['type'],
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
                        'type' => $score['type'],
                        'num' => $score['num'],
                    ),
                ), $oldAll);
            }
        }

        //阶段判断更新
        foreach ($scoreItem as $score) {
            $key = $this->existScoreItem($score, $oldItem);
            if ($key !== false) {
                if ($score['score'] != $oldItem[$key]['score']) {
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

        foreach ($newAll as $key => $value) {
            $newAll[$key] = array(
                'account' => $value['account'],
                'score' => $value['score'],
                'term' => $value['term'],
                'lessonId' => $lessonNeed[$value['code']],
                'type' => $value['type'],
                'num' => $value['num'],
            );
        }

        foreach ($newItem as $key => $value) {
            $newItem[$key] = array(
                'account' => $value['account'],
                'score' => $value['score'],
                'term' => $value['term'],
                'lessonId' => $lessonNeed[$value['code']],
                'type' => $value['type'],
            );
        }

        //总成绩插入更新
        if (count($updateAll) > 0) {
            $scoreAllDb->update($updateAll);
        }
        //总成绩插入新增
        if (count($newAll) > 0) {
            $scoreAllDb->insert($newAll);
        }
        //阶段插入更新
        if (count($updateItem) > 0) {
            $scoreItemDb->update($updateItem);
        }
        //阶段插入新增
        if (count($newItem) > 0) {
            $scoreItemDb->insert($newItem);
        }

        $terms = array();
        $termAvg = array();
        $yearAvg = array();
        $allAvg = array(
            'scoreT' => 0,
            'numT' => 0,
            'scoreF' => 0,
            'numF' => 0,
        );
        //总成绩排序
        foreach ($oldAll as $score) {
            if ( ! isset($terms[$score['term']])) {
                $terms[$score['term']] = array(
                    'all' => array(),
                    'item' => array(),
                );
                $termAvg[$score['term']] = array(
                    'scoreT' => 0,
                    'numT' => 0,
                    'scoreF' => 0,
                    'numF' => 0,
                );
                if ( ! isset($yearAvg[substr($score['term'], 0, 9)])) {
                    $yearAvg[substr($score['term'], 0, 9)] = array(
                        'scoreT' => 0,
                        'numT' => 0,
                        'scoreF' => 0,
                        'numF' => 0,
                    );
                }
            }
            $terms[$score['term']]['all'][] = array(
                'name' => $score['name'],
                'score' => $score['score'],
                'num' => $score['num'],
            );
            if (is_numeric($score['score'])) {
                if ($score['type'] != '通识教育选修课') {
                    $termAvg[$score['term']]['scoreT'] += $score['score'] * $score['num'];
                    $termAvg[$score['term']]['numT'] += $score['num'];
                    $termAvg[$score['term']]['scoreF'] += $score['score'] * $score['num'];
                    $termAvg[$score['term']]['numF'] += $score['num'];
                    $yearAvg[substr($score['term'], 0, 9)]['scoreT'] += $score['score'] * $score['num'];
                    $yearAvg[substr($score['term'], 0, 9)]['numT'] += $score['num'];
                    $yearAvg[substr($score['term'], 0, 9)]['scoreF'] += $score['score'] * $score['num'];
                    $yearAvg[substr($score['term'], 0, 9)]['numF'] += $score['num'];
                    $allAvg['scoreT'] += $score['score'] * $score['num'];
                    $allAvg['numT'] += $score['num'];
                    $allAvg['scoreF'] += $score['score'] * $score['num'];
                    $allAvg['numF'] += $score['num'];
                } else {
                    $termAvg[$score['term']]['scoreT'] += $score['score'] * $score['num'];
                    $termAvg[$score['term']]['numT'] += $score['num'];
                    $yearAvg[substr($score['term'], 0, 9)]['scoreT'] += $score['score'] * $score['num'];
                    $yearAvg[substr($score['term'], 0, 9)]['numT'] += $score['num'];
                    $allAvg['scoreT'] += $score['score'] * $score['num'];
                    $allAvg['numT'] += $score['num'];
                }
            }
        }
        //阶段排序
        foreach ($oldItem as $score) {
            if ( ! isset($terms[$score['term']])) {
                $terms[$score['term']] = array(
                    'all' => array(),
                    'item' => array(),
                );
            }
            $terms[$score['term']]['item'][] = array(
                'name' => $score['name'],
                'score' => $score['score'],
                'type' => $score['type'],
            );
        }
        //计算加权
        foreach ($termAvg as $key => $value) {
            if ($value['numT'] == 0) {
                unset($termAvg[$key]);
                continue;
            }
            if ($value['numF'] != 0) {
                $termAvg[$key] = array(
                    't' => number_format($value['scoreT'] / $value['numT'], 2),
                    'f' => number_format($value['scoreF'] / $value['numF'], 2),
                );
            } else {
                $termAvg[$key] = array(
                    't' => number_format($value['scoreT'] / $value['numT'], 2),
                );
            }
        }
        foreach ($yearAvg as $key => $value) {
            if ($value['numT'] == 0) {
                unset($yearAvg[$key]);
                continue;
            }
            if ($value['numF'] != 0) {
                $yearAvg[$key] = array(
                    't' => number_format($value['scoreT'] / $value['numT'], 2),
                    'f' => number_format($value['scoreF'] / $value['numF'], 2),
                );
            } else {
                $yearAvg[$key] = array(
                    't' => number_format($value['scoreT'] / $value['numT'], 2),
                    'f' => '',
                );
            }
        }

        if ($allAvg['numT'] != 0) {
            if ($allAvg['numF'] != 0) {
                $allAvg = array(
                    't' => number_format($allAvg['scoreT'] / $allAvg['numT'], 2),
                    'f' => number_format($allAvg['scoreF'] / $allAvg['numF'], 2),
                );
            } else {
                $allAvg = array(
                    't' => number_format($allAvg['scoreT'] / $allAvg['numT'], 2),
                );
            }
        } else {
            $allAvg = array(
                't' => '暂无',
                'f' => '暂无',
            );
        }

        if ( ! $flag) {
            $update = array(
                'all' => array(),
                'item' => array(),
            );
        }
        return array(
            'avg' => array(
                'term' => $termAvg,
                'year' => $yearAvg,
                'all' => $allAvg,
            ),
            'term' => $terms,
            'update' => $update,
        );
    }

    private function existScore($score, $arr)
    {
        foreach ($arr as $key => $value) {
            if ($score['code'] === $value['code']) {
                return $key;
            }
        }
        return false;
    }

    private function existScoreItem($score, $arr)
    {
        foreach ($arr as $key => $value) {
            if ($score['code'] === $value['code'] && $score['type'] === $value['type']) {
                return $key;
            }
        }
        return false;
    }
}