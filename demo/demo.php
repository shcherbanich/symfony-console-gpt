#!/usr/bin/env php
<?php

require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

$app = new \Demo\Console\DemoApp();

$app->add(new \Demo\Console\Command\ShowPhpFileContentCommand());
$app->add(new \Demo\Console\Command\ListPhpFilesCommand());
$app->add(new \Demo\Console\Command\GetCurrentTimeCommand());

$app->run();
