<?php

declare(strict_types=1);

namespace Demo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class  ShowPhpFileContentCommand extends Command
{
    public function __construct()
    {
        parent::__construct('app:show-file');
    }

    // Configure the command with a description and an argument
    protected function configure(): void
    {
        $this
            ->setDescription('Displays the content of a PHP file.')
            ->setHelp('This command allows you to display the content of a PHP file by providing its path.')
            ->addArgument(
                'filePath',
                InputArgument::REQUIRED,
                'The path to the PHP file you want to display.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('filePath');

        if (!file_exists($filePath)) {
            $output->writeln('<error>The specified file does not exist: ' . $filePath . '</error>');
            return Command::FAILURE;
        }

        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
            $output->writeln('<error>The specified file is not a PHP file: ' . $filePath . '</error>');
            return Command::FAILURE;
        }

        $content = file_get_contents($filePath);
        $output->writeln('<info>Content of the file:</info>');
        $output->writeln('=================================');
        $output->writeln($content);
        $output->writeln('=================================');

        return Command::SUCCESS;
    }
}
