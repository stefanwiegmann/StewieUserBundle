<?php

namespace Stewie\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
// use Symfony\Component\Config\FileLocator;
// use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class StewieUserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        // define parameters
        $container->setParameter('stewie_user.register', $config['register']);
        $container->setParameter('stewie_user.max_rows', $config['max_rows']);
        $container->setParameter('stewie_user.from_email', $config['from_email']);
    }
}
