<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use marvin255\bxmigrate\repo\Files;
use marvin255\bxmigrate\checker\HighLoadIb;
use marvin255\bxmigrate\cli\Notifier;
use marvin255\bxmigrate\manager\Simple;
use marvin255\bxmigrate\Exception;
use InvalidArgumentException;

/**
 * Консольная команда для Symfony console, которая создает новую миграцию.
 */
class SymphonyCreate extends Command
{
    /**
     * @var string
     */
    protected $migrationPath = null;

    public function __construct($migrationPath)
    {
        if (empty($migrationPath)) {
            throw new InvalidArgumentException('Migration path can not be empty');
        }
        $this->migrationPath = $migrationPath;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('bxmigrate:create')
            ->setDescription('Create new migration')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Create new migration'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $repo = new Files($this->migrationPath);
        $checker = new HighLoadIb();
        $notifier = new Notifier($output);
        $manager = new Simple($repo, $checker, $notifier);
        $manager->create($name);
    }
}
