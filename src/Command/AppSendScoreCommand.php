<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\RedisService;
use App\Service\ScoreSortService;
use App\Service\WechatService;
use Nefu\Nefuer;

class AppSendScoreCommand extends Command
{
    protected static $defaultName = 'app:send-score';
    private $redisService;
    private $scoreService;
    private $wechatService;

    public function __construct(RedisService $redisService, ScoreSortService $scoreService, WechatService $wechatService)
    {
        parent::__construct();
        $this->redisService = $redisService;
        $this->scoreService = $scoreService;
        $this->wechatService = $wechatService;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->redisService->autoPop('score', function($data) {
            $data = json_decode($data, true);
            $nefuer = new Nefuer();
            $nefuer->login($data['account'], $data['password']);
            $scores = $this->scoreService->getScore($data['account'], $nefuer);
            $updates = $scores['update'];
            foreach ($updates['all'] as $update) {
                var_dump($this->wechatService->sendScore(
                    $data['openid'],
                    $update['name'],
                    $update['score'],
                    '总成绩',
                    $update['num'],
                    $update['update']
                ));
            }
            if ($data['item']) {
                foreach ($updates['item'] as $update) {
                    switch ($update['type']) {
                        case 'itm1':
                            $update['type'] = '阶段1';
                            break;
                        case 'itm2':
                            $update['type'] = '阶段2';
                            break;
                        case 'itm3':
                            $update['type'] = '阶段3';
                            break;
                        case 'nml':
                            $update['type'] = '平时成绩';
                            break;
                        case 'mid':
                            $update['type'] = '期中';
                            break;
                        case 'fin':
                            $update['type'] = '期末';
                            break;
                    }
                    var_dump($this->wechatService->sendScore(
                        $data['openid'],
                        $update['name'],
                        $update['score'],
                        $update['type'],
                        $update['num'],
                        $update['update']
                    ));
                }
            }
        });
    }

}
