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
            if ($item->getName() === '成绩') {
                if ($this->checkItem($item->getAccount(), $list)) {
                    $this->redisService->push('scoreWithItem', $item->getAccount());
                } else {
                    $this->redisService->push('scoreWithoutItem', $item->getAccount());
                }
            } else {
                switch ($item->getName()) {
                    case '考试':
                        $this->redisService->push('exam', $item->getAccount());
                        break;
                    case '阶段成绩':
                        //nothing
                        break;
                }
            }
        }
    }

    private function checkItem($account, $list) {
        foreach ($list as $item) {
            if ($item->getName() === '阶段成绩' && $item->getAccount() === $account) {
                return true;
            }
        }
        return false;
    }
}
