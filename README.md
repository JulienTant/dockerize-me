# Dockerize Me!

`dockerize-me` is a simple, yet powerful utility that let you use docker for your php project, leveraging docker-compose.

It will do 4 things...

* Try to guess what framework you are using.
* Create a docker/ folder with the definitions of the images (Dockerfile) we will use.
* Create a docker-compose.yml file that describe the services.
* Create a 'dcp' tool that will streamline your development workflow with docker.

...and provides 4 containers:

* app: Nginx server & PHP-FPM.
* mysql: a MySQL server.
* redis: a Redis server.
* node: a container to run npm or yarn. 

## Installation

### Globally

The first option is to install it globally using composer:

`composer global require jtant/dockerize-me`

You must make sure that the `$HOME/.composer/vendor/bin` folder is present in your `$PATH` env variable.

### Per project

If you don't want to install it globally, you can also choose to install it per project using : `composer require jtant/dockerize-me` in your project.

### Which version will can I get ?

* PHP: 7.1 - Available : 7.0, 7.1
* MySQL: 5.7 - Available: https://hub.docker.com/_/mysql/
* Redis: 3.2 - Available: https://hub.docker.com/_/redis/
* Node: latest - Available: https://hub.docker.com/_/node/

You can change versions by using `--php`, `--mysql`, `--redis`, `--node` while calling `dockerize-me`.

## How to use it ?

### Globally

Go to your project and type `dockerize-me`.

### Per project

Go to your project and type `./vendor/bin/dockerize-me`.

### Available options

* `--project-name=xx` - change the name of your project. By default, it will be the folder name.
* `--php=xx` - choose your PHP version
* `--mysql=xx` - choose your mysql version
* `--redis=xx` - choose your redis version
* `--node=xx` - choose your node version
* `--with-blackfire` - adds Blackfire (see https://github.com/JulienTant/dockerize-me/wiki/Installing-Blackfire)
* `--force` - overrides files if they already exists. **If you've modified a generated file, you will loose your modifications! Use with caution**
* `--no-interaction` or `-n` - automatically answers the default options to the questions.
* `-h` - show all the options

This list may not be exhaustive, I recommend you use  `dockerize-me -h` to see all options.

### Both

After that first step, you may want to change the docker-compose.yml file to update the database information - or change your framework to use those informations.

To start the containers, just run `docker-compose run -d` or `./dcp up`.

## What is dcp ?

`dcp` is a utility that has been installed on your project, it will give your some shortcuts to work with docker, and help your keep a not complicated workflow.

Here is what it can do:

* `dcp`: show the running container in your project.
* `dcp up`: starts the docker containers for your projects.
* `dcp down`: stops and remove docker containers for your projects (not the volumes).
* `dcp reload/restart/rs/rl`: shortcuts for `dcp up && dcp down`.
* `dcp test`: execute `./vendor/bin/phpunit` in a new app container.
* `dcp t`: execute `./vendor/bin/phpunit` into the app container.
* `dcp composer`: execute `composer` into the app container.
* `dcp yarn`: execute `yarn` in a node container.
* `dcp npm`: execute `npm` in a node container.
* (Laravel specific) `dcp artisan`: execute an `php artisan` into the app container.
* (Symfony2 specific) `dcp app/console`: execute `php app/console` into the app container.
* (Symfony3 specific) `dcp bin/console`: execute `php bin/console` into the app container.

As a fallback, any other command will be passed to `docker-compose`.

I invite you to change the `dcp` file if needed to fit it to your needs!

If you use `dcp` a lot, feel free to add this alias in your .bashrc/.zshrc file : `alias dcp="./dcp"`.

## How to customize your containers

The Dockerfiles are all located in the `docker/` folder. You can do anything you want in there.

After you've done your changes, you must rebuild the containers using `./dcp build --no-cache`.

If you containers are running, you can restart them using `./dcp rs`, or just run `./dcp up` if they were not.