<?php

namespace Stewie\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class StefanwiegmannUserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
      // $loader = new YamlFileLoader(
      //     $container,
      //     new FileLocator(__DIR__.'/../../../../config/packages')
      // );
      // $loader->load('stefanwiegmann_user.yaml');
    }
}
