<?php

namespace Stewie\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stewie\UserBundle\Entity\User;
// use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Stewie\UserBundle\Service\PathFinder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Stewie\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;
use Stewie\UserBundle\Service\RoleUpdater;
use Doctrine\Common\Collections\ArrayCollection;

class FillUsersCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'stewie:user:fill-users';

    private $em;
    private $passwordEncoder;
    private $avatarGenerator;
    private $pathFinder;
    private $roleUpdater;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, AvatarGenerator $avatarGenerator, PathFinder $pathFinder, RoleUpdater $roleUpdater)
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->avatarGenerator = $avatarGenerator;
        $this->pathFinder = $pathFinder;
        $this->roleUpdater = $roleUpdater;
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
        // $em = $this->container->get('doctrine')->getManager();
        $repo = $this->em->getRepository('StewieUserBundle:User');
        $roleRepo = $this->em->getRepository('StewieUserBundle:Role');
        $groupRepo = $this->em->getRepository('StewieUserBundle:Group');

        $contents = file_get_contents($this->pathFinder->getBundlePath().'Resources/data/users.json');
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
                    // $user->clearAssociations();

                     foreach ($user->getGroups() as &$group){
                         $group->removeUser($user);
                     }

                    foreach ($user->getUserRoles() as &$role){
                        $role->removeUser($user);
                    }

                    $user->setRoles = new ArrayCollection();
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
                $this->em->persist($user);
                $this->em->flush();

                $this->roleUpdater->updateUser($user);

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $output->writeln('');
        // $output->writeln($this->pathFinder->getBundlePath());
        return 1;
    }
}
