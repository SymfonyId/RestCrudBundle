<?php

namespace Symfonian\Indonesia\RestCrudBundle;

use Symfonian\Indonesia\BundlePlugins\PluginBundle as Bundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonianIndonesiaRestCrudBundle extends Bundle
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('services.yml');
    }

    public function getAlias()
    {
        return 'symfonyid_rest_crud';
    }
}