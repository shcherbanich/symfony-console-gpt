<?php

declare(strict_types=1);

namespace Demo\Console\Command;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GetCurrentTimeCommand extends Command
{
    public function __construct()
    {
        parent::__construct('app:get-current-time');
    }

    protected function configure(): void
    {
        $this->setDescription('Get current time');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(( new DateTime())->format('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }
}
