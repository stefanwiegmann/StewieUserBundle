<?php

namespace Stewie\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
// use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class StewieUserExtension extends Extension
{

    public function getNamespace()
    {
        return 'http://stewie.com/schema/dic/user';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        // define parameters
        $container->setParameter('stewie_user.register', $config['register']);
        $container->setParameter('stewie_user.max_rows', $config['max_rows']);
        $container->setParameter('stewie_user.from_name', $config['from_name']);
        $container->setParameter('stewie_user.from_email', $config['from_email']);

        // load services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('commands.xml');
    }
}
