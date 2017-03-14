<?php

namespace marvin255\bxmigrate\cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use marvin255\bxmigrate\Exception;
use InvalidArgumentException;

/**
 * Консольная команда для Symfony console, которая применяет миграции.
 */
class SymphonyUp extends Command
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
        $this
            ->setName('bxmigrate:up')
            ->setDescription('Set up migrations')
            ->addArgument(
                'count',
                InputArgument::OPTIONAL,
                'Count of migrations to set up'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = (int) $input->getArgument('count');
        $count = $count ? $count : null;
        try {
            $repo = new \marvin255\bxmigrate\repo\Files($this->migrationPath);
            $checker = new \marvin255\bxmigrate\checker\HighLoadIb();
            $manager = new \marvin255\bxmigrate\manager\Simple($repo, $checker);
            $messages = $manager->up($count);
            foreach ($messages as $message) {
                $output->writeln('<info>'.$message.'</info>');
            }
        } catch (Exception $e) {
            $output->writeln('<error>'.get_class($e).': '.$e->getMessage().'</error>');
        }
    }
}
