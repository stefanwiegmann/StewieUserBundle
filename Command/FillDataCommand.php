<?php

namespace Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

class FillDataCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:fill-data';

    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
      $this
          // the short description shown while running "php bin/console list"
          ->setDescription('Fills all data for UserBundle.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to create users, roles and groups')

          // add all or only static groups
          ->addOption('all')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $statusCommand = $this->getApplication()->find('user:fill-status');
      $roleCommand = $this->getApplication()->find('user:fill-roles');
      $groupCommand = $this->getApplication()->find('user:fill-groups');
      $userCommand = $this->getApplication()->find('user:fill-users');
      // $noInput = new ArrayInput();

      if($input->getOption('all')){

            $arguments = [
                '--all'  => true,
            ];

            $outputText = 'All data filled!';

    }else{

          $arguments = [
              '--all'  => false,
          ];

          $outputText = 'Static data filled!';
    }

      $commandInput = new ArrayInput($arguments);

      $returnCode = $statusCommand->run($commandInput, $output);
      $returnCode = $roleCommand->run($commandInput, $output);
      $returnCode = $groupCommand->run($commandInput, $output);
      $returnCode = $userCommand->run($commandInput, $output);
      $output->writeln('All data filled!');

      return 1;
    }
}
