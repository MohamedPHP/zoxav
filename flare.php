<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

use App\Commands\ZoxavCommand;

$application = new Application();

// register commands
$application->add(new ZoxavCommand());

$application->run();
