<?php

declare(strict_types=1);

namespace Demo\Console\Command;

use DateTime;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ListPhpFilesCommand extends Command
{
    public function __construct()
    {
        parent::__construct('app:list-php-files');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Lists all PHP files in the current project directory with their last modification time')
            ->setHelp('This command scans the current project directory and lists all PHP files recursively');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Scanning for PHP files in the current project...</info>');

        $projectDir = getcwd();

        if (!is_dir($projectDir)) {
            $output->writeln('<error>Current directory does not exist or is not accessible.</error>');
            return Command::FAILURE;
        }

        $phpFiles = [];
        $directoryIterator = new RecursiveDirectoryIterator($projectDir, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directoryIterator);

        foreach ($iterator as $file) {
            $pathname = $file->getPathname();
            $pathname = str_replace($projectDir . DIRECTORY_SEPARATOR, '', $pathname);

            if (str_starts_with($pathname, 'vendor')) {
                continue;
            }

            /** @var SplFileInfo $file */
            if ($file->getExtension() === 'php') {
                $phpFiles[] = $pathname . ' ' . ( new DateTime("@{$file->getMTime()}"))->format('Y-m-d H:i:s');
            }
        }

        if (empty($phpFiles)) {
            $output->writeln('<comment>No PHP files found in the project directory.</comment>');
            return Command::SUCCESS;
        }

        foreach ($phpFiles as $data) {
            $output->writeln($data);
        }

        $output->writeln('<info>PHP file list completed.</info>');

        return Command::SUCCESS;
    }
}
