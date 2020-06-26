<?php

namespace Stewie\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
// use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;

class WipeDataCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'stewie:user:wipe-data';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
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
        // wipe users
        $output->writeln('Wiping users:');
        $repo = $this->em->getRepository('StewieUserBundle:User');
        $users = $repo->findAll();

        $progressBar = new ProgressBar($output, count($users));
        $progressBar->start();

        foreach ($users as &$item){

            $this->em->remove($item);
            $progressBar->advance();
        }

        $this->em->flush();
        $progressBar->finish();
        $output->writeln('');

        // wipe groups
        $output->writeln('Wiping groups:');
        $repo = $this->em->getRepository('StewieUserBundle:Group');
        $groups = $repo->findAll();

        $progressBar = new ProgressBar($output, count($groups));
        $progressBar->start();

        foreach ($groups as &$item){

            $this->em->remove($item);
            $progressBar->advance();
        }

        $this->em->flush();
        $progressBar->finish();
        $output->writeln('');

        // wipe roles
        $output->writeln('Wiping roles:');
        $repo = $this->em->getRepository('StewieUserBundle:Role');
        $roles = $repo->findAll();

        $progressBar = new ProgressBar($output, count($roles));
        $progressBar->start();

        foreach ($roles as &$item){

            $this->em->remove($item);
            $progressBar->advance();
        }

        $this->em->flush();
        $progressBar->finish();
        $output->writeln('');

        // wipe status
        $output->writeln('Wiping status:');
        $repo = $this->em->getRepository('StewieUserBundle:Status');
        $status = $repo->findAll();

        $progressBar = new ProgressBar($output, count($status));
        $progressBar->start();

        foreach ($status as &$item){

            $this->em->remove($item);
            $progressBar->advance();
        }

        $this->em->flush();
        $progressBar->finish();
        $output->writeln('');

        // wipe logs
        $output->writeln('Wiping logs:');
        $repo = $this->em->getRepository('StewieUserBundle:UserLogEntry');
        $logs = $repo->findAll();

        $progressBar = new ProgressBar($output, count($logs));
        $progressBar->start();

        foreach ($logs as &$item){

            $this->em->remove($item);
            $progressBar->advance();
        }

        $this->em->flush();
        $progressBar->finish();
        $output->writeln('');

        // end of script
        $output->writeln('All data wiped!');

        return 1;
    }
}
