<?php

namespace Stewie\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stewie\UserBundle\Entity\Group;
// use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Stewie\UserBundle\Service\AvatarGenerator;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Stewie\UserBundle\Service\PathFinder;

class FillGroupsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'stewie:user:fill-groups';

    private $em;
    private $pathFinder;
    private $avatarGenerator;

    public function __construct(EntityManagerInterface $em, PathFinder $pathFinder, AvatarGenerator $avatarGenerator)
    {
        parent::__construct();
        $this->em = $em;
        $this->pathFinder = $pathFinder;
        $this->avatarGenerator = $avatarGenerator;
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
        // $em = $this->container->get('doctrine')->getManager();
        $repo = $this->em->getRepository('StewieUserBundle:Group');
        $roleRepo = $this->em->getRepository('StewieUserBundle:Role');

        $contents = file_get_contents($this->pathFinder->getBundlePath().'Resources/data/groups.json');
        $contents = utf8_encode($contents);
        $results = json_decode($contents, true);

        $progressBar = new ProgressBar($output, count($results));
        $output->writeln('Fill groups:');
        $progressBar->start();

        foreach ($results as &$item){

            if($item['essential'] || $input->getOption('all')){

                $group = $repo->findOneByName($item['name']);

                if(!$group){
                    $group = new Group;
                }else{
                    foreach ($group->getGroupRoles() as &$role){
                        $group->removeGroupRole($role);
                    }
                }

                $group->setName($item['name']);
                $group->setDescription($item['description']);
                $group->setAvatarName($this->avatarGenerator->create($group));

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

                $this->em->persist($group);
                $this->em->flush();

                $progressBar->advance();
                // $output->writeln('Group '.$group->getName().' created or updated!');
              }
        }

        // $output->writeln('All Groups created or updated!');
        $progressBar->finish();
        $output->writeln('');
        return 1;
      }
}
