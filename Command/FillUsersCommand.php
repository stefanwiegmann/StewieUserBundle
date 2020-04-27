<?php

namespace Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stefanwiegmann\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Stefanwiegmann\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;
// use Doctrine\Common\Collections\ArrayCollection;

class FillUsersCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'user:fill-users';

    private $container;
    private $passwordEncoder;
    private $avatarGenerator;

    public function __construct(ContainerInterface $container, UserPasswordEncoderInterface $passwordEncoder, AvatarGenerator $avatarGenerator)
    {
        parent::__construct();
        $this->container = $container;
        $this->passwordEncoder = $passwordEncoder;
        $this->avatarGenerator = $avatarGenerator;
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

      $contents = file_get_contents($this->container->get('kernel')->locateResource('@StefanwiegmannUserBundle/Data')."/users.json");
      $contents = utf8_encode($contents);
      $results = json_decode($contents, true);

      $progressBar = new ProgressBar($output, count($results));
      $output->writeln('Fill users:');
      $progressBar->start();

      // get all groups to unassigne
      $groups = $groupRepo->findAll();

      foreach ($results as &$item){

        $userRoles = array();

        if($item['essential'] || $input->getOption('all')){

          $user = $repo->findOneByUsername($item['username']);

          // create if user does not exist
          if(!$user){
              $user = new User;
          }else{
             // or remove all UserRoles
             $user->clearAssociations();
          }

          // set values
          $user->setUsername($item['username']);
          $user->setFirstname($item['first_name']);
          $user->setLastname($item['last_name']);
          $user->setEmail($item['email']);
          $user->setAvatarName($this->avatarGenerator->create($user));
          $user->setPassword($this->passwordEncoder->encodePassword(
                       $user,
                       $item['password']
                   ));

           foreach ($item['roles'] as &$role){
             $user->addUserRole($roleRepo->findOneByName($role));
           }

           foreach ($item['groups'] as &$group){
             $user->addGroup($groupRepo->findOneByName($group));
           }

          // persist
          $repo->refreshRoles($user);
          $em->persist($user);
          $em->flush();

          $progressBar->advance();
        }
      }

      $progressBar->finish();
      $output->writeln('');
      return 1;
    }
}
