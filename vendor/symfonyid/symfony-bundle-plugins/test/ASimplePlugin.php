<?php

namespace Symfonian\Indonesia\BundlePlugins\Tests;

use Symfonian\Indonesia\BundlePlugins\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ASimplePlugin extends Plugin
{
    public function name()
    {
        return 'a_simple_plugin';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $container->setParameter('a_simple_plugin.loaded', true);
    }
}
