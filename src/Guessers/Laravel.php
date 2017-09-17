<?php
namespace DockerizeMe\Guessers;

class Laravel implements Guessable
{
    /**
     * Tells if the projects is of that kind
     *
     * @param $basePath string
     * @return bool
     */
    public function guess($basePath)
    {
        return is_file($basePath .'/artisan') && is_file($basePath .'/public/index.php');
    }

    /**
     * Return the shortname of the framework
     *
     * @return string
     */
    public function name()
    {
        return "laravel";
    }

    /**
     * Give some tips about how to configure the framework
     *
     * @return string
     */
    public function tips()
    {
        return <<<TIPS
- Make sure you update your .env to match the docker-compose.yml database parameters. The DB_HOST will be "mysql" and REDIS_HOST will be "redis".
TIPS;
    }
}
