#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use CakeDay\Commands\GenerateScheduleCsvCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$command = new GenerateScheduleCsvCommand();

$application->add($command);
$application->setDefaultCommand($command->getName(), true);
$application->run();
