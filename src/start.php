<?php

namespace DockerizeMe;

use Cocur\Slugify\Slugify;
use DockerizeMe\Guessers\Guesser;
use DockerizeMe\Guessers\Laravel;
use DockerizeMe\Guessers\Normal;
use DockerizeMe\Guessers\Symfony;
use Symfony\Component\Console\Application;

function start()
{
    $slugify = new Slugify();
    $guesser = new Guesser(new Laravel(), new Symfony(), new Normal());

    $application = new Application();
    $application->add(new Commands\DockerizeMe($slugify, $guesser));
    $application->setDefaultCommand('dockerize-me');
    $application->run();
}