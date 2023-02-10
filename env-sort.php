<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\EnvSortCommand;
use Symfony\Component\Console\Application;


$application = new Application();
$application->add(new EnvSortCommand());
$application->run();