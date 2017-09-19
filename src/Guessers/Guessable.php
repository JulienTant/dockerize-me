<?php
namespace DockerizeMe\Guessers;


interface Guessable
{
    /**
     * Tells if the projects is of that kind
     *
     * @param $basePath string
     * @return bool
     */
    public function guess($basePath);
}