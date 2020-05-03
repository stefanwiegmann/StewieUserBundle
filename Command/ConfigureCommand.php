<?php

namespace Stewie\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
// use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
// use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ConfigureCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'stewie:user:configure';

    // private $container;
    //
    // public function __construct(ContainerInterface $container)
    // {
    //     parent::__construct();
    //     $this->container = $container;
    // }

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

          // double check what to do
          // ->addArgument('what', InputArgument::REQUIRED, 'Type `Y` if you want all seetings to be updated.')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // what should be done?
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select what you want to have configured (defaults to nothing, backups will be made)',
            ['n' => 'nothing', 'f' => 'Create needed folders', 'R' => 'Routing only', 'S' => 'Services only', 'b' => 'Bundle config file only', 'ALL' => 'All of the above'],
            'n'
        );
        $question->setErrorMessage('%s is an invalid option.');

        $decision = $helper->ask($input, $output, $question);

        // setup
        switch ($decision) {
            case 'n':
                return 0;
            case 'ALL':
                $progressBar = new ProgressBar($output, 5);
                break;
            default:
                $progressBar = new ProgressBar($output, 2);
        }

        $output->writeln('Start configuration:');
        $progressBar->start();

        $filesystem = new Filesystem();

        // are we a vendor or the dev?
        $bundlePath = $this->getBundlePath($filesystem);
        $progressBar->advance();

        // create missing folders
        if($decision == 'f' || $decision == 'ALL'){

            $this->updateFilesystem($filesystem, $bundlePath);
            $progressBar->advance();

        }

        // create missing folders
        if($decision == 'b' || $decision == 'ALL'){

            $this->updateBundleConfig($filesystem, $bundlePath);
            $progressBar->advance();

        }

        // copy routes from bundle
        if($decision == 'R' || $decision == 'ALL'){

            $this->updateRoutes($filesystem, $bundlePath);
            $progressBar->advance();

        }

        // copy service config from bundle
        if($decision == 'S' || $decision == 'ALL'){

            $this->updateServices($filesystem, $bundlePath);
            $progressBar->advance();

        }

        // finish and out
        $progressBar->finish();
        $output->writeln('');
        // $output->writeln($input->getArgument('what'));
        return 1;
    }

    protected function updateBundleConfig($filesystem, $bundlePath)
    {
        $original = 'config/packages/stewie_user.yaml';
        $bundleFile = $bundlePath.'Resources/config/packages/stewie_user.yaml';
        $filesystem->remove($original.'.old');
        $filesystem->rename($original, $original.'.old');
        $filesystem->copy($bundleFile, $original);

        return true;
    }

    protected function updateServices($filesystem, $bundlePath)
    {
        $original = 'config/services.yaml';
        $bundleFile = $bundlePath.'Resources/config/services.yaml';
        $filesystem->remove($original.'.new');
        $filesystem->touch($original.'.new');
        $location = 'outside';
        $originalLines = file($original);
        $bundleLines = file($bundleFile);

        // copy all lines but stewie_user lines
        foreach ($originalLines as &$line) {

            // find start of section
            if(substr($line,0,17) == '    # stewie_user'){
              $location = 'inside';
            }

            // copy over, if other content
            if($location == 'outside'){
              $filesystem->appendToFile($original.'.new', $line);
            }

            // find end of section
            if(substr($line,0,21) == '    # stewie_user end'){
              $location = 'outside';
            }

        }

        // switch to bundle file and append
        $filesystem->appendToFile($original.'.new', "\n");
        foreach ($bundleLines as &$line) {

            $filesystem->appendToFile($original.'.new', $line);

        }
        $filesystem->appendToFile($original.'.new', "\n");

        $filesystem->remove($original.'.old');
        $filesystem->rename($original, $original.'.old');
        $filesystem->rename($original.'.new', $original);

        return true;
    }

    protected function updateRoutes($filesystem, $bundlePath)
    {
        $original = 'config/routes.yaml';
        $bundleFile = $bundlePath.'Resources/config/routes.yaml';
        $filesystem->remove($original.'.new');
        $filesystem->touch($original.'.new');
        $location = 'outside';
        $originalLines = file($original);
        $bundleLines = file($bundleFile);

        // copy all lines but stewie_user lines
        foreach ($originalLines as &$line) {

            // find start of section
            if(substr($line,0,12) == 'stewie_user:'){
              $location = 'inside';
            }

            // copy over, if other content
            if($location == 'outside'){
              $filesystem->appendToFile($original.'.new', $line);
            }

            // find end of section
            if(substr($line,0,16) == "#stewie_user_end"){
              $location = 'outside';
            }

        }

        // switch to bundle file and append
        $filesystem->appendToFile($original.'.new', "\n");
        foreach ($bundleLines as &$line) {

            $filesystem->appendToFile($original.'.new', $line);

        }
        $filesystem->appendToFile($original.'.new', "\n");

        $filesystem->remove($original.'.old');
        $filesystem->rename($original, $original.'.old');
        $filesystem->rename($original.'.new', $original);

        return true;
    }

    protected function updateFilesystem($filesystem, $bundlePath)
    {

        if(!$filesystem->exists('var/stewie/user-bundle/avatar/')){

            $filesystem->mkdir('var/stewie/user-bundle/avatar/');

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
}
