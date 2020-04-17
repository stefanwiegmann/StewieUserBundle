<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;

class WipeDataCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:wipe-data';

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
          ->setDescription('Wipes all data for UserBundle.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to wipe users, roles and groups')

          // add all or only static groups
          ->addOption('all')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $em = $this->container->get('doctrine')->getManager();

      // wipe users
      $output->writeln('Wiping users:');
      $repo = $em->getRepository('StefanwiegmannUserBundle:User');
      $users = $repo->findAll();

      $progressBar = new ProgressBar($output, count($users));
      $progressBar->start();

      foreach ($users as &$item){

        $em->remove($item);
        $progressBar->advance();
        }

      $em->flush();
      $progressBar->finish();
      $output->writeln('');

      // wipe groups
      $output->writeln('Wiping groups:');
      $repo = $em->getRepository('StefanwiegmannUserBundle:Group');
      $groups = $repo->findAll();

      $progressBar = new ProgressBar($output, count($groups));
      $progressBar->start();

      foreach ($groups as &$item){

        $em->remove($item);
        $progressBar->advance();
        }

      $em->flush();
      $progressBar->finish();
      $output->writeln('');

      // wipe roles
      $output->writeln('Wiping roles:');
      $repo = $em->getRepository('StefanwiegmannUserBundle:Role');
      $roles = $repo->findAll();

      $progressBar = new ProgressBar($output, count($roles));
      $progressBar->start();

      foreach ($roles as &$item){

        $em->remove($item);
        $progressBar->advance();
        }

      $em->flush();
      $progressBar->finish();
      $output->writeln('');

      // wipe status
      $output->writeln('Wiping status:');
      $repo = $em->getRepository('StefanwiegmannUserBundle:Status');
      $status = $repo->findAll();

      $progressBar = new ProgressBar($output, count($status));
      $progressBar->start();

      foreach ($status as &$item){

        $em->remove($item);
        $progressBar->advance();
        }

      $em->flush();
      $progressBar->finish();
      $output->writeln('');

      // end of script
      $output->writeln('All data wiped!');

      return 1;
    }
}
