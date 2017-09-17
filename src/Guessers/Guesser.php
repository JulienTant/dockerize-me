<?php

namespace DockerizeMe\Guessers;


class Guesser
{
    /**
     * @var array[Guessable]
     */
    private $drivers = [];

    /**
     * @var Guessable
     */
    private $default;

    public function __construct(...$drivers)
    {
        $this->drivers = $drivers;
        $this->default = new Normal();
    }

    public function isValid($name)
    {
        foreach ($this->drivers as $driver) {
            if ($name == $driver->name()) {
                return true;
            }
        }

        return false;
    }

    public function find($basePath)
    {
        foreach ($this->drivers as $driver) {
            if ($driver->guess($basePath)) {
                return $driver;
            }
        }

        return $this->default;
    }
}