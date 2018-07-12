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
        echo '<pre>';
        var_dump($this->listPushesService->list());die;
    }
}
