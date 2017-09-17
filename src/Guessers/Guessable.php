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

    /**
     * Return the shortname of the framework
     *
     * @return string
     */
    public function name();

    /**
     * Give some tips about how to configure the framework
     *
     * @return string
     */
    public function tips();
}