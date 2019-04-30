<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Stefanwiegmann\UserBundle\Entity\Group;
use App\Stefanwiegmann\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FillSampleCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:fill-sample';

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
          ->setDescription('Fills sample data.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to fill sample data...')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:User');
      $groupRepo = $em->getRepository('StefanwiegmannUserBundle:Group');

      // create groups
      $groups = array();
      array_push($groups, array('name' => 'Schalke'));
      array_push($groups, array('name' => 'Bayern'));
      array_push($groups, array('name' => 'Dortmund'));

      foreach ($groups as &$item){

        $group = $groupRepo->findOneByName($item['name']);

        if(!$group){
            $group = new Group;
        }

        $group->setName($item['name']);

        $em->persist($group);
        $em->flush();

        $output->writeln('Group '.$group->getName().' created or updated!');
      }

      // create users
      $users = array();
      array_push($users, array('firstname' => 'Huub', 'lastname' => 'Stevens', 'group' => 'Schalke'));
      array_push($users, array('firstname' => 'Guido', 'lastname' => 'Burgstaller', 'group' => 'Schalke'));
      array_push($users, array('firstname' => 'Suat', 'lastname' => 'Serdar', 'group' => 'Schalke'));
      array_push($users, array('firstname' => 'Weston', 'lastname' => 'McKinnie', 'group' => 'Schalke'));
      array_push($users, array('firstname' => 'Alessandro', 'lastname' => 'Schoepf', 'group' => 'Schalke'));
      array_push($users, array('firstname' => 'Lucien', 'lastname' => 'Favre', 'group' => 'Dortmund'));
      array_push($users, array('firstname' => 'Julian', 'lastname' => 'Weigel', 'group' => 'Dortmund'));
      array_push($users, array('firstname' => 'Marco', 'lastname' => 'Reus', 'group' => 'Dortmund'));
      array_push($users, array('firstname' => 'Sebastian', 'lastname' => 'Wolf', 'group' => 'Dortmund'));
      array_push($users, array('firstname' => 'Mario', 'lastname' => 'Goetze', 'group' => 'Dortmund'));
      array_push($users, array('firstname' => 'Nico', 'lastname' => 'Kovac', 'group' => 'Bayern'));
      array_push($users, array('firstname' => 'Manuel', 'lastname' => 'Neuer', 'group' => 'Bayern'));
      array_push($users, array('firstname' => 'Mats', 'lastname' => 'Hummels', 'group' => 'Bayern'));
      array_push($users, array('firstname' => 'Frank', 'lastname' => 'Ribery', 'group' => 'Bayern'));
      array_push($users, array('firstname' => 'Joshua', 'lastname' => 'Kimmich', 'group' => 'Bayern'));

      foreach ($users as &$item){

        $user = $repo->findOneByEmail($item['firstname'].'.'.$item['lastname'].'@stefanwiegmann.de');

        if(!$user){
            $user = new User;
        }

        // set values
        $user->setUsername($item['firstname'].'.'.$item['lastname'].'@stefanwiegmann.de');
        $user->setFirstname($item['firstname']);
        $user->setLastname($item['lastname']);
        $user->setEmail($item['firstname'].'.'.$item['lastname'].'@stefanwiegmann.de');
        $user->setPassword($this->passwordEncoder->encodePassword(
                     $user,
                     'password'
                 ));
        $group = $groupRepo->findOneByName($item['group']);
        $group->addUser($user);
        $em->persist($group);

        // persist
        $em->persist($user);
        $em->flush();

        $output->writeln('User '.$user->getUserName().' created or updated!');
      }

      // get all roles to assign
      // $roles = $roleRepo->findAll();
      // $roleNames = array();
      //
      // foreach ($roles as &$item){
      //   array_push($roleNames, $item->getName());
      // }
      //
      // // create if user does not exist or remove all UserRoles
      // $user = $repo->findOneByUsername('admin');
      //
      // if(!$user){
      //     $user = new User;
      // }else{
      //     foreach ($user->getUserRole() as &$role){
      //       $user->removeUserRole($role);
      //     }
      //     foreach ($groups as &$group){
      //       $group->removeUser($user);
      //       $em->persist($group);
      //     }
      // }
      //
      // // set values
      // $user->setUsername('admin');
      // $user->setFirstname('admin');
      // $user->setLastname('admin');
      // $user->setEmail('admin@admin.net');
      // $user->setPassword($this->passwordEncoder->encodePassword(
      //              $user,
      //              'password'
      //          ));
      // $user->setRoles($roleNames);
      // foreach ($roles as &$role){
      //   $user->addUserRole($role);
      // }
      // foreach ($groups as &$group){
      //   $group->addUser($user);
      //   $em->persist($group);
      // }
      //
      // // persist
      // $em->persist($user);
      // $em->flush();

      $output->writeln('User admin created or updated!');
    }
}
