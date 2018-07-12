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


class AppSendExamCommand extends Command
{
    protected static $defaultName = 'app:send-exam';
    private $redisService;
    private $wechatService;

    public function __construct(RedisService $redisService, ScoreSortService $scoreService, WechatService $wechatService)
    {
        parent::__construct();
        $this->redisService = $redisService;
        $this->wechatService = $wechatService;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->redisService->autoPop('exam', function($data) {
            $data = json_decode($data, true);
            var_dump($data);
        });
    }

}
