<?php

namespace App\Stefanwiegmann\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StefanwiegmannUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerForAutoconfiguration(EntityRepository::class)
            ->addTag('doctrine.repository_service');
    }
}
