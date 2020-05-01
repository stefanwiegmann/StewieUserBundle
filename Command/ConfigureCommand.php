<?php

namespace Stewie\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class ConfigureCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'stewie:user:configure';

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
          ->setDescription('Updates the configuration of the user-bundle.')

          // the full command description shown when running the command with
          // the "--help" option
          ->setHelp('This command allows you to configure the user-bundle')

          // add all or only static groups
          ->addOption('all')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

      $progressBar = new ProgressBar($output, 4);
      $output->writeln('Start configuration:');
      $progressBar->start();

      $filesystem = new Filesystem();

      // are we a vendor or the dev?
      $bundlePath = $this->getBundlePath($filesystem);
      $progressBar->advance();

      // // create missing folders
      $this->updateFilesystem($filesystem, $bundlePath);
      $progressBar->advance();

      // // step 3
      // updateGitIgnore($filesystem);
      // $progressBar->advance();

      // // copy bundle config
      // $filesystem->copy($path.'Resources/config/packages/stewie_user.yaml', 'config/packages/stewie_user.yaml', true);
      // $progressBar->advance(); // step 4

      // finish and out
      $progressBar->finish();
      $output->writeln('');
      return 1;
    }

    protected function updateFilesystem($filesystem, $bundlePath)
    {

        if(!$filesystem->exists('var/stewie/user-bundle/uploads/avatar/')){

            $filesystem->mkdir('var/stewie/user-bundle/uploads/avatar/');

        }

        return true;
    }

    protected function getBundlePath($filesystem)
    {

        if($filesystem->exists('lib/stewie/user-bundle')){

            $path = 'lib/stewie/user-bundle/';

        }elseif($filesystem->exists('vendor/stewie/user-bundle')){

            $path = 'vendor/stewie/user-bundle/';

        }

        return $path;
    }

    // protected function updateGitIgnore($filesystem)
    // {
    //
    // // make sure upload folders are in .gitignore
    // $filesystem->remove('.gitignore.new');
    // $filesystem->touch('.gitignore.new');
    // $filename = ".gitignore";
    // $lines = file($filename);
    // $location = 'outside';
    //
    // // copy all lines but stewie_user lines
    // foreach ($lines as &$line) {
    //   // find start of section
    //   if(substr($line,0,27) == '###> stewie/user-bundle ###'){
    //     $location = 'inside';
    //   }
    //
    //   // copy over, if other content
    //   if($location == 'outside'){
    //     $filesystem->appendToFile('.gitignore.new', $line);
    //   }
    //
    //   // find end of section
    //   if(substr($line,0,27) == $filesystem"###< stewie/user-bundle ###"){
    //     $location = 'outside';
    //   }
    // }$filesystem
    //
    // // add new gitignore directives
    // $filesystem->appendToFile('.gitignore.new', "\n###> stewie/user-bundle ###\n");
    // $filesystem->appendToFile('.gitignore.new', "!/var/stewie/user-bundle/uploads/avatar/.gitkeep\n");
    // $filesystem->appendToFile('.gitignore.new', "###< stewie/user-bundle ###\n");
    //
    // // overwrite file
    // $filesystem->remove('.gitignore');
    // $filesystem->rename('.gitignore.new', '.gitignore');
    //
    // // and out
    // return true;
    // }
}
