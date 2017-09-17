# Dockerize Me!

`dockerize-me` is simple, yet powerful utility that let you use docker for your php project, leveraging docker-compose.

It will do 3 things :

* Try to guess what framework you are using.
* Create a docker/ folder with the definitions of the images (Dockerfile) we will use.
* Create a docker-compose.yml file that describe the services.
* Create a 'dcp' tool that will streamline your development workflow with docker.

## Installation

### Globally

The first option is to install it globally using composer :

`composer global require jtant/dockerize-me`

You must make sure that the `$HOME/.composer/vendor/bin` folder is present in your `$PATH` env variable

### Per project

If you don't want to install it globally, you can also choose to install it per project using : `composer require jtant/dockerize-me` in your project. 

## How to use it ?

### Globally

Go to your project and type `dockerize-me`. You can see the options available using `dockerize-me -h`.

### Per project

Go to your project and type `./vendor/bin/dockerize-me`. You can see the options available using `./vendor/bin/dockerize-me -h`.

### Both

After that first step, you may want to change the docker-compose.yml file to update the database information - or change your framework to use those informations.

To start the 

## What is dcp ?

`dcp` is a utility that has been installed on your project, that will give your some shortcuts to work with docker, and help your keep a not complicated workflow.

Here is what it can do :

* `dcp up`: starts the docker containers for your projects.
* `dcp down`: stops and remove docker containers for your projects (not the volumes).
* `dcp reload/restart/rs/rl`: shortcuts for `dcp up && dcp down`
* `dcp test`: execute `./vendor/bin/phpunit` in a new app container
* `dcp t`: execute `./vendor/bin/phpunit` into the app container
* `dcp composer`: execute `composer` into the app container
* `dcp yarn`: execute `yarn` in a node container
* `dcp npm`: execute `npm` in a node container
* (Laravel specific) `dcp artisan`: execute an `php artisan` into the app container
* (Symfony2 specific) `dcp app/console`: execute `php app/console` into the app container
* (Symfony3 specific) `dcp bin/console`: execute `php bin/console` into the app container

I invite you to change the `dcp` file if needed to fit it to your needs!