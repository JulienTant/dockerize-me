<?php

namespace DockerizeMe;

use Cocur\Slugify\Slugify;
use DockerizeMe\Guessers\Guesser;
use DockerizeMe\ProjectTypes\Laravel;
use DockerizeMe\ProjectTypes\Normal;
use DockerizeMe\ProjectTypes\Symfony;
use League\Plates\Engine;
use Symfony\Component\Console\Application;

function start()
{
    $slugify = new Slugify();
    $guesser = new Guesser(new Laravel(), new Symfony(), new Normal());
    $tplEngine = new Engine(__DIR__ .'/stubs', 'tpl');

    $application = new Application();
    $application->add(new Commands\DockerizeMe($slugify, $guesser, $tplEngine));
    $application->setDefaultCommand('dockerize-me');
    $application->run();
}