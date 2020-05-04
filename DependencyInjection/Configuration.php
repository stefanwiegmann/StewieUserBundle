<?php

namespace Stewie\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('stewie_user');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->integerNode('max_rows')
                    ->defaultValue(10)
                    ->end()
                ->scalarNode('from_email')
                    ->defaultValue('name@email.de')
                    ->end()
                ->scalarNode('register')
                    ->defaultValue(false)
                    ->end()
                ->arrayNode('routing')
                    ->children()
                        ->scalarNode('after_login')
                            ->defaultValue('index')
                            ->end()
                        ->scalarNode('after_logout')
                            ->defaultValue('index')
                            ->end()
                    ->end()
                ->end() // routing
            ->end()
        ;

        return $treeBuilder;
    }
}
