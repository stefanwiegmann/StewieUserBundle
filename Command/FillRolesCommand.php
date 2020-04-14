<?php

namespace App\Stefanwiegmann\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Stefanwiegmann\UserBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;

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

          // add all or only static groups
          ->addOption('all')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $em = $this->container->get('doctrine')->getManager();
      $repo = $em->getRepository('StefanwiegmannUserBundle:Role');

      $contents = file_get_contents($this->container->getParameter('kernel.project_dir')."/src/Stefanwiegmann/UserBundle/Data/roles.json");
      $contents = utf8_encode($contents);
      $results = json_decode($contents, true);

      $progressBar = new ProgressBar($output, count($results));
      $output->writeln('Fill roles:');
      $progressBar->start();

      foreach ($results as &$item){

        $role = $repo->findOneByName($item['name']);

        if(!$role){
            $role = new Role;
        }

        $role->setName($item['name']);
        $role->setSort($item['sort']);
        $role->setDescription($item['description']);

        $em->persist($role);
        $em->flush();

        $progressBar->advance();
        }

      $progressBar->finish();
      $output->writeln('');
      return 1;
    }
}
