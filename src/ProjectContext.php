<?php
namespace DockerizeMe;


use DockerizeMe\ProjectTypes\ProjectType;

class ProjectContext
{
    /**
     * @var string
     */
    public $projectName;
    /**
     * @var ProjectType
     */
    public $projectType;
    /**
     * @var string
     */
    public $phpVersion;
    /**
     * @var string
     */
    public $mysqlVersion;
    /**
     * @var string
     */
    public $redisVersion;
    /**
     * @var string
     */
    public $nodeVersion;

    /**
     * @var bool
     */
    public $withBlackfire;

    public function toArray()
    {
        return get_object_vars($this);
    }
}