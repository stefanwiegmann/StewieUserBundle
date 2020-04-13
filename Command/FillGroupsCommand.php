<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Stefanwiegmann\UserBundle\Entity\Group;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

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

          // add all or only static groups
          ->addOption('all')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:Group');
      $roleRepo = $em->getRepository('StefanwiegmannUserBundle:Role');

      $contents = file_get_contents($this->container->getParameter('kernel.project_dir')."/src/Stefanwiegmann/UserBundle/Data/groups.json");
      $contents = utf8_encode($contents);
      $results = json_decode($contents, true);

      foreach ($results as &$item){

        if($item['essential'] || $input->getOption('all')){

          $group = $repo->findOneByName($item['name']);

            if(!$group){
                $group = new Group;
            }else{
                foreach ($group->getGroupRole() as &$role){
                  $group->removeGroupRole($role);
                }
            }

            $group->setName($item['name']);
            $group->setDescription($item['description']);

            if ($item['name'] == 'App Admin'){
            // get all roles to assign to Administrators
              $roles = $roleRepo->findAll();
              foreach ($roles as &$item){
                 $group->addGroupRole($item);
                }
            }else{
            // get roles from json file
              foreach ($item['roles'] as &$role){
                $role = $roleRepo->findOneByName($role);
                $group->addGroupRole($role);
              }
            }

            $em->persist($group);
            $em->flush();

            $output->writeln('Group '.$group->getName().' created or updated!');
          }
        }

        $output->writeln('All Groups created or updated!');
        return 1;
      }
}
