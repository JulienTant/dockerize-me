<?php
namespace DockerizeMe\ProjectTypes;


use DockerizeMe\Guessers\Guessable;

class Normal extends ProjectType implements Guessable
{
    /**
     * Tells if the projects is of that kind
     *
     * @param $basePath string
     * @return bool
     */
    public function guess($basePath)
    {
        return true;
    }

    /**
     * Return the shortname of the framework
     *
     * @return string
     */
    public function name()
    {
        return "normal";
    }
    /**
     * Give some tips about how to configure the framework
     *
     * @return string
     */
    public function tips()
    {
        return "";
    }
}