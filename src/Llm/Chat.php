<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;

interface Chat
{
    public static function create(SymfonyStyle $io, Application $app): Chat;

    public function run(): void;
}
