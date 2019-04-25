<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Stefanwiegmann\UserBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FillRolesCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:fill-roles';

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
          ->setDescription('Creates a basic set of roles.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to create roles...')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:Role');

      $role = $repo->findOneByName('ROLE_APP_ADMIN');

      if(!$role){
          $role = new Role;
      }

      $role->setName('ROLE_APP_ADMIN');
      $role->setTranslationKey('name.role_app_admin');

      $em->persist($role);
      $em->flush();

      $output->writeln('Role '.$role->getName().' created or update!');
    }
}
