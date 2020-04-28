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
                ->integerNode('max_rows')->end()
                ->scalarNode('from_email')->end()
                ->scalarNode('register')->end()
                ->arrayNode('routing')
                    ->children()
                        ->scalarNode('after_login')->end()
                        ->scalarNode('after_logout')->end()
                    ->end()
                ->end() // routing
            ->end()
        ;

        return $treeBuilder;
    }
}
