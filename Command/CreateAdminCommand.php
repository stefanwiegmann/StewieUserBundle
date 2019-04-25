<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Stefanwiegmann\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:create-admin';

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
          ->setDescription('Creates a new admin.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to create a admin...')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:User');
      $roleRepo = $em->getRepository('StefanwiegmannUserBundle:Role');

      // get all roles to assign
      $roles = $roleRepo->findAll();
      $roleNames = array();

      foreach ($roles as &$item){
        array_push($roleNames, $item->getName());
      }

      // create if user does not exist or remove all UserRoles
      $user = $repo->findOneByUsername('admin');

      if(!$user){
          $user = new User;
      }else{
          foreach ($user->getUserRole() as &$item){
            $user->removeUserRole($item);
          }
      }

      // set values
      $user->setUsername('admin');
      $user->setFirstname('admin');
      $user->setLastname('admin');
      $user->setEmail('admin@admin.net');
      $user->setPassword($this->passwordEncoder->encodePassword(
                   $user,
                   'password'
               ));
      $user->setRoles($roleNames);
      foreach ($roles as &$item){
        $user->addUserRole($item);
      }

      // persist
      $em->persist($user);
      $em->flush();

      $output->writeln('User admin created or updated!');
    }
}
