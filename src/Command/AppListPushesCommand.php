<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\RedisService;

class AppListPushesCommand extends Command
{
    protected static $defaultName = 'app:list-pushes';
    private $redisService;

    public function __construct(RedisService $redisService)
    {
        parent::__construct();
        $this->redisService = $redisService;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
