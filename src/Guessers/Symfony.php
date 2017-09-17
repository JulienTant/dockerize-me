<?php
namespace DockerizeMe\Guessers;

class Symfony implements Guessable
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
            is_file($basePath .'/web/app.php')
            && (is_file($basePath .'/bin/console') || is_file($basePath .'/app/console'));
    }

    /**
     * Return the shortname of the framework
     *
     * @return string
     */
    public function name()
    {
        return "symfony";
    }

    /**
     * Give some tips about how to configure the framework
     *
     * @return string
     */
    public function tips()
    {
        return <<<TIPS
- You may want to change your app_dev.php file to update or remove the condition that checks your IP address
- Make sure you update your parameter.yml to match the docker-compose.yml database parameters. The database_host will be "mysql".
TIPS;
    }
}