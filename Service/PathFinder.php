<?php

namespace Stewie\UserBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PathFinder extends AbstractController
{
    public function getBundlePath(){

        $filesystem = new Filesystem();
        $path = 'Path not found!';

        if($filesystem->exists('lib/stewie/user-bundle')){

            $path = 'lib/stewie/user-bundle/';

        }elseif($filesystem->exists('vendor/stewie/user-bundle')){

            $path = 'vendor/stewie/user-bundle/';

        }

        return $path;
    }
}
