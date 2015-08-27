<?php

namespace Symfonian\Indonesia\BundlePlugins\Tests;

use Symfonian\Indonesia\BundlePlugins\PluginInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BarPlugin implements PluginInterface
{
    public function name()
    {
        return 'bar';
    }

    public function load(array $pluginConfiguration, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('bar.yml');
    }

    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        $pluginNode
            ->children()
                ->scalarNode('bar')
                ->end()
            ->end();
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SetParameterCompilerPass('bar.build_was_called', true));
    }

    public function boot(ContainerInterface $container)
    {
        $container->get('bar.boot')->call();
    }
}
