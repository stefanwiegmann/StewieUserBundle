<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
// use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Stefanwiegmann\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateAdminCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:create-admin';

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
          ->setDescription('Creates a new admin.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to create a admin...')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      // $em = $this->getDoctrine ()->getManager ();
      $em = $this->container->get('doctrine')->getManager();
        $admin = new User;
        $admin->setUsername('admin');
        $admin->setPassword('password');

        $em->persist($admin);
        $em->flush();

      $output->write('You are about to ');
      $output->write('create a user.');
    }
}
