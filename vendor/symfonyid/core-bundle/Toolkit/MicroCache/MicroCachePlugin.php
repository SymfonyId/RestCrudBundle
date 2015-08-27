<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\MicroCache;

use Symfonian\Indonesia\BundlePlugins\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class MicroCachePlugin extends Plugin
{
    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('services.yml');
    }

    public function isRequireConfigurationKey()
    {
        return false;
    }

    public function name()
    {
        return 'micro_cache';
    }
}
