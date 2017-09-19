<?php

namespace DockerizeMe\ProjectTypes;


abstract class ProjectType
{
    /**
     * Return the shortname of the framework
     *
     * @return string
     */
    abstract public function name();

    /**
     * Give some tips about how to configure the framework
     *
     * @return string
     */
    abstract public function tips();
}