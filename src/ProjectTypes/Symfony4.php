<?php
namespace DockerizeMe\ProjectTypes;

use DockerizeMe\Guessers\Guessable;

class Symfony4 extends ProjectType implements Guessable
{
    /**
     * Tells if the projects is of that kind
     *
     * @param $basePath string
     * @return bool
     */
    public function guess($basePath)
    {
        return
            is_file($basePath .'/public/index.php') && is_file($basePath .'/bin/console');
    }

    /**
     * Return the shortname of the framework
     *
     * @return string
     */
    public function name()
    {
        return "symfony4";
    }

    /**
     * Give some tips about how to configure the framework
     *
     * @return string
     */
    public function tips()
    {
        return <<<TIPS
- Make sure you update your configuration to match the docker-compose.yml database parameters.
TIPS;
    }
}
