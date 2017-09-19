<?php

namespace DockerizeMe\Commands;

use Cocur\Slugify\Slugify;
use DockerizeMe\Guessers\Guesser;
use DockerizeMe\ProjectContext;
use League\Plates\Engine;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DockerizeMe extends Command
{
    const INVALID_PHP_VERSION = 1;
    const DOCKER_FOLDER_EXISTS = 2;
    const DOCKER_COMPOSER_FILE_EXISTS = 3;

    /**
     * @var Slugify
     */
    private $slugifier;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var Guesser
     */
    private $guesser;
    /**
     * @var Engine
     */
    private $templates;

    public function __construct(Slugify $slugify, Guesser $guesser, Engine $tplEngine)
    {
        $this->slugifier = $slugify;
        $this->guesser = $guesser;
        $this->templates = $tplEngine;

        parent::__construct();
    }

    protected function configure()
    {
        $position = explode(DIRECTORY_SEPARATOR, getcwd());

        $this->setName('dockerize-me')
            ->setDescription('Add some docker magic to your PHP application')
            ->setHelp('This command adds a docker-compose file to your projects and the Dockerfile associated.')
            ->addOption('project-name', null, InputOption::VALUE_REQUIRED, 'Project name (will be slugified)', $this->slugifier->slugify(end($position)))
            ->addOption('php', null, InputOption::VALUE_REQUIRED, 'Wanted php version (7.0 | 7.1)', '7.1')
            ->addOption('mysql', null, InputOption::VALUE_REQUIRED, 'Wanted mysql version', '5.7')
            ->addOption('redis', null, InputOption::VALUE_REQUIRED, 'Wanted redis version', '3.2')
            ->addOption('node', null, InputOption::VALUE_REQUIRED, 'Wanted node version', 'latest')
            ->addOption('with-blackfire', null, InputOption::VALUE_NONE, 'Install blackfire');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;


        $ctx = new ProjectContext;
        $ctx->projectName = $this->slugifier->slugify($input->getOption('project-name'));
        $ctx->projectType = $this->guesser->find(realpath('.'));
        $ctx->phpVersion = $input->getOption('php');
        $ctx->mysqlVersion = $input->getOption('mysql');
        $ctx->redisVersion = $input->getOption('redis');
        $ctx->nodeVersion = $input->getOption('node');
        $ctx->withBlackfire = $input->getOption('with-blackfire');

        $this->ensurePHPVersion($ctx);
        $this->ensureFilesDoesntExists();


        $this->sayHello();
        $this->showSelectedVersions($ctx);
        $this->askContinue($input, $output);

        $this->copyStubs($ctx);

        $tips = $ctx->projectType->tips();
        if ($tips != "") {
            $this->output->writeln("");
            $this->infoln("Tips for your project type:");
            $this->commentln($tips);
            $this->output->writeln("");
        }


        $this->infoln("");
        $this->commentln("We're ready!");
        $this->infoln("You can now customize your docker-compose.yml file, and then `./dcp up`.");
        $this->infoln("Remember that MySQL & Redis are not accessible on 127.0.0.1 from your php application,");
        $this->infoln("but from their name : mysql / redis.");
        if ($ctx->withBlackfire) {
            $this->commentln("Blackfire: don't forget to set the BLACKFIRE_SERVER_ID and BLACKFIRE_SERVER_TOKEN variables in the `docker-compose.yml` file");
        }
    }

    private function ensurePHPVersion(ProjectContext $ctx)
    {
        $possibilities = ["7.0", "7.1"];
        foreach ($possibilities as $possibility) if ($ctx->phpVersion === $possibility) {
            return;
        }

        $this->errorln("PHP version must be one of the following : [7.0|7.1]");
        exit(self::INVALID_PHP_VERSION);
    }

    private function ensureFilesDoesntExists()
    {
        if (is_dir('./docker')) {
            $this->errorln("It looks like a docker/ folder already exists. Please make a backup and delete it before using me!");
            exit(self::DOCKER_FOLDER_EXISTS);

        }
        if (is_file('./docker-compose.yml')) {
            $this->errorln("It looks like a docker-compose.yml file already exists. Please make a backup and delete it before using me!");
            exit(self::DOCKER_COMPOSER_FILE_EXISTS);
        }
    }

    protected function sayHello()
    {
        $this->infoln("Hi ! It looks like you want to give some docker magic to your project!");
        $this->infoln("let me help you with that!");
        $this->infoln("");
        $this->infoln("We're going to use the current stack:");
    }

    /**
     * @param ProjectContext $ctx
     */
    protected function showSelectedVersions(ProjectContext $ctx)
    {
        $this->writeVersion("Project name", $ctx->projectName);
        $this->writeVersion("Project type", $ctx->projectType->name());

        $this->writeVersion("PHP", $ctx->phpVersion);
        $this->writeVersion("MySQL", $ctx->mysqlVersion);
        $this->writeVersion("Redis", $ctx->redisVersion);
        $this->writeVersion("NodeJS", $ctx->nodeVersion);
        if ($ctx->withBlackfire) {
            $this->writeVersion("Blackfire", "installed");
        }

        $this->output->writeln("");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function askContinue(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("<info>Is that ok?</info> <comment>[Y/n]</comment>\n", true);
        if (!$helper->ask($input, $output, $question)) {
            $this->infoln("Please call me with `-h` to see available options");
            exit(0);
        }
    }

    /**
     * @param ProjectContext $ctx
     */
    protected function copyStubs(ProjectContext $ctx)
    {
        $this->info("Working...");

        $path = $this->templates->getDirectory();
        $target = realpath('.');

        $this->templates->addData($ctx->toArray());

        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($objects as $name => $object) {
            if ($object->isDir()) {
                mkdir($target . DIRECTORY_SEPARATOR . $objects->getSubPathName());
            } elseif ($object->isFile()) {
                $filePath = str_replace('.tpl', '', $objects->getSubPathName());

                if (substr($object->getFilename(), 0, 2) === "__") {
                    continue;
                }

                $content = $this->templates->render($filePath);

                file_put_contents($target . DIRECTORY_SEPARATOR . $filePath, $content);
                unset($content);
            }
        }
        $this->infoln(" done.");

        if (!chmod($target . DIRECTORY_SEPARATOR . 'dcp', 0755)) {
            $this->commentln('Warning: please set chmod 755 on ' . $target . DIRECTORY_SEPARATOR . 'dcp !');
        }
    }


    private function info($str)
    {
        $this->output->write('<info>' . $str . '</info>');
    }

    private function infoln($str)
    {
        $this->output->writeln('<info>' . $str . '</info>');
    }

    private function errorln($str)
    {
        $this->output->writeln('<error>' . $str . '</error>');
    }

    private function writeVersion($tool, $version)
    {
        $this->output->writeln("\t<info>" . $tool . ":</info> <comment>" . $version . "</comment>");
    }

    private function commentln($str)
    {
        $this->output->writeln('<comment>' . $str . '</comment>');
    }
}