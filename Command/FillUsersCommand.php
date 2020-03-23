<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Stefanwiegmann\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Console\Input\InputOption;

class FillUsersCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:fill-users';

    private $container;
    private $passwordEncoder;

    public function __construct(ContainerInterface $container, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->container = $container;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
      $this
          // the short description shown while running "php bin/console list"
          ->setDescription('Creates new users.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to create users...')

          // add all or only static groups
          ->addOption('all')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:User');
      $roleRepo = $em->getRepository('StefanwiegmannUserBundle:Role');
      $groupRepo = $em->getRepository('StefanwiegmannUserBundle:Group');

      $contents = file_get_contents($this->container->getParameter('kernel.project_dir')."/src/Stefanwiegmann/UserBundle/Data/users.json");
      $contents = utf8_encode($contents);
      $results = json_decode($contents, true);

      // get all groups to unassigne
      $groups = $groupRepo->findAll();

      foreach ($results as &$item){

        $userRoles = array();

        if($item['static'] || $input->getOption('all')){

          // create if user does not exist or remove all UserRoles
          $user = $repo->findOneByUsername($item['username']);

          if(!$user){
              $user = new User;
          }else{
              foreach ($user->getUserRole() as &$role){
                $user->removeUserRole($role);
              }
              foreach ($groups as &$group){
                $group->removeUser($user);
                $em->persist($group);
              }
          }

          // set values
          $user->setUsername($item['username']);
          $user->setFirstname($item['first_name']);
          $user->setLastname($item['last_name']);
          $user->setEmail($item['email']);
          $user->setPassword($this->passwordEncoder->encodePassword(
                       $user,
                       $item['password']
                   ));

           foreach ($item['roles'] as &$role){
             $userRole = $roleRepo->findOneByName($role);
             $user->addUserRole($userRole);
             array_push($userRoles, $userRole->getName());
           }

           foreach ($item['groups'] as &$group){
             // $output->writeln($groupRepo->findOneByName($group)->getId());
             // $user->addGroup($groupRepo->findOneByName($group));
             // $userGroup = $groupRepo->findOneByName($group);
             $userGroup = $groupRepo->findOneByName($group);
             $userGroup->addUser($user);
             foreach ($userGroup->getGroupRole() as &$groupRole){
               array_push($userRoles, $groupRole->getName());
             }
             $em->persist($userGroup);
           }


          // $user->setRoles($roleNames);
          // foreach ($roles as &$role){
          //   $user->addUserRole($role);
          // }
          // foreach ($groups as &$group){
          //   $group->addUser($user);
          //   $em->persist($group);
          // }

          // persist
          $user->setRoles($userRoles);
          $em->persist($user);
          $em->flush();

          $output->writeln('User '.$user->getUsername().' created or updated!');
        }
      }

      $output->writeln('All Users created or updated!');
      return 1;
    }
}
