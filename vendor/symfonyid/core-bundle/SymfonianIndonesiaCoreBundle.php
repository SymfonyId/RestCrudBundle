<?php

namespace Symfonian\Indonesia\CoreBundle;

use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Compiler\ManagerRegister;
use Symfonian\Indonesia\BundlePlugins\PluginBundle as Bundle;
use Symfonian\Indonesia\CoreBundle\Toolkit\MicroCache\MicroCachePlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonianIndonesiaCoreBundle extends Bundle
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Toolkit/Resources/config'));
        $loader->load('services.yml');
    }

    public function addCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ManagerRegister());
    }

    public function defaultPlugins()
    {
        return array(new MicroCachePlugin());
    }

    public function getAlias()
    {
        return 'symfonyid_core';
    }
}
