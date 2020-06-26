<?php

namespace Stewie\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stewie\UserBundle\Entity\Status;
// use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Stewie\UserBundle\Service\PathFinder;

class FillStatusCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'stewie:user:fill-status';

    private $em;
    private $pathFinder;

    public function __construct(EntityManagerInterface $em, PathFinder $pathFinder)
    {
        parent::__construct();
        $this->em = $em;
        $this->pathFinder = $pathFinder;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a complete set of status.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create status...')

            // add all or only static groups
            ->addOption('all')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $em = $this->container->get('doctrine')->getManager();
        $repo = $this->em->getRepository('StewieUserBundle:Status');

        $contents = file_get_contents($this->pathFinder->getBundlePath().'Resources/data/status.json');
        $contents = utf8_encode($contents);
        $results = json_decode($contents, true);

        $progressBar = new ProgressBar($output, count($results));
        $output->writeln('Fill status:');
        $progressBar->start();

        foreach ($results as &$item){

            $status = $repo->findOneByName($item['name']);

            if(!$status){
                $status = new Status;
            }

            $status->setName($item['name']);
            $status->setTranslationKey($item['translationKey']);

            $this->em->persist($status);
            $this->em->flush();

            $progressBar->advance();
        }

        $progressBar->finish();
        $output->writeln('');
        return 1;
    }
}
