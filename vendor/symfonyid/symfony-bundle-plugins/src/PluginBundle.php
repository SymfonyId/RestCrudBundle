<?php

namespace Symfonian\Indonesia\BundlePlugins;

use Symfony\Component\Console\Application;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Extend your bundle from this class. It allows users to register plugins for this bundle by providing them as
 * constructor arguments.
 *
 * The bundle itself can have no container extension or configuration anymore. Instead, you can introduce something
 * like a `CorePlugin`, which is registered as a `PluginInterface` for this bundle. Return an instance of it from your
 * bundle's `defaultPlugins()` method.
 */
abstract class PluginBundle extends Bundle
{
    /**
     * @var PluginInterface[]
     */
    private $registeredPlugins = array();

    /**
     * Bundle alias. Used for config key.
     *
     * @return string
     */
    abstract public function getAlias();

    /**
     * Override this method to add bundle config tree.
     *
     * @param ArrayNodeDefinition $rootNode
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode)
    {
    }

    /**
     * Override this method to register compiler pass.
     *
     * @param ContainerBuilder $container
     */
    public function addCompilerPass(ContainerBuilder $container)
    {
    }

    public function load(array $config, ContainerBuilder $container)
    {
    }

    final public function __construct(array $plugins = array())
    {
        foreach ($this->defaultPlugins() as $plugin) {
            $this->registerPlugin($plugin);
        }

        foreach ($plugins as $plugin) {
            $this->registerPlugin($plugin);
        }
    }

    /**
     * @inheritdoc
     */
    final public function build(ContainerBuilder $container)
    {
        $this->addCompilerPass($container);
        foreach ($this->registeredPlugins as $plugin) {
            $plugin->build($container);
        }
    }

    /**
     * @inheritdoc
     */
    final public function boot()
    {
        foreach ($this->registeredPlugins as $plugin) {
            $plugin->boot($this->container);
        }
    }

    final public function registerCommands(Application $application)
    {
        parent::registerCommands($application);

        foreach ($this->registeredPlugins as $plugin) {
            $plugin->registerCommands($application);
        }
    }

    /**
     * Provide any number of `PluginInterface`s that should always be registered.
     *
     * @return PluginInterface[]
     */
    protected function defaultPlugins()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    final public function getContainerExtension()
    {
        return new PluginExtension($this);
    }

    /**
     * Register a plugin for this bundle.
     *
     * @param PluginInterface $plugin
     */
    private function registerPlugin(PluginInterface $plugin)
    {
        $this->registeredPlugins[] = $plugin;
    }

    /**
     * @return array $plugins
     */
    public function getPlugins()
    {
        return $this->registeredPlugins;
    }
}
