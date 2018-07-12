<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\MoveHereService;

class AppMoveHereCommand extends Command
{
    protected static $defaultName = 'app:move-here';
    private $moveHereService;

    public function __construct(MoveHereService $moveHereService)
    {
        parent::__construct();
        $this->moveHereService = $moveHereService;
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->moveHereService->moveHere();
    }
}
