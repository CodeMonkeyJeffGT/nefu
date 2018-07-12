<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\RedisService;
use App\Service\ListPushesService;

class AppListPushesCommand extends Command
{
    protected static $defaultName = 'app:list-pushes';
    private $redisService;
    private $listPushesService;

    public function __construct(RedisService $redisService, ListPushesService $listPushesService)
    {
        parent::__construct();
        $this->redisService = $redisService;
        $this->listPushesService = $listPushesService;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $list = $this->listPushesService->list();
        foreach ($list as $item) {
            switch ($item['name']) {
                case '成绩':
                    $info = array(
                        'account' => $item['account'],
                        'openid' => $item['openid'],
                        'item' => $this->checkItem($item['account'], $list),
                    );
                    $this->redisService->getRedis()->publish('score', json_encode($info));
                    break;
                case '阶段成绩':
                    //nothing
                    break;
                case '考试':
                    $this->redisService->push('exam', $item['account']);
                    break;
            }
        }
    }

    private function checkItem($account, $list) {
        foreach ($list as $key => $item) {
            if ($item['name'] === '阶段成绩' && $item['account'] === $account) {
                return true;
            }
        }
        return false;
    }
}
