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

      $roles = array();
      array_push($roles, array('name' => 'ROLE_APP_ADMIN'));
      array_push($roles, array('name' => 'ROLE_USER_ADMIN'));
      array_push($roles, array('name' => 'ROLE_USER_REGISTER'));
      array_push($roles, array('name' => 'ROLE_USER_VIEW'));

      foreach ($roles as &$item){

        $role = $repo->findOneByName($item['name']);

        if(!$role){
            $role = new Role;
        }

        $role->setName($item['name']);
        $role->setTranslationKey('name.'.\strtolower($item['name']));

        $em->persist($role);
        $em->flush();

        $output->writeln('Role '.$role->getName().' created or updated!');
      }
    }
}