<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Stefanwiegmann\UserBundle\Entity\Group;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FillGroupsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:fill-groups';

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
          ->setDescription('Creates a basic set of groups.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to create groups...')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:Group');
      $roleRepo = $em->getRepository('StefanwiegmannUserBundle:Role');

      $groups = array();
      array_push($groups, array('name' => 'Administrators'));

      foreach ($groups as &$item){

        $group = $repo->findOneByName($item['name']);

        if(!$group){
            $group = new Group;
        }else{
            foreach ($group->getGroupRole() as &$role){
              $group->removeGroupRole($role);
            }
        }

        $group->setName($item['name']);

        // get all roles to assign to Administrators
        $roles = $roleRepo->findAll();
        foreach ($roles as &$item){
           $group->addGroupRole($item);
          }

        $em->persist($group);
        $em->flush();

        $output->writeln('Group '.$group->getName().' created or updated!');
      }
    }
}
