<?php

namespace DockerizeMe;

use Symfony\Component\Console\Application;

function start()
{
    $application = new Application();
    $application->add(new Commands\DockerizeMe());
    $application->setDefaultCommand('dockerize-me');
    $application->run();
}