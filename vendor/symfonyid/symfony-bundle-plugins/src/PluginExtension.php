<?php

namespace Symfonian\Indonesia\BundlePlugins;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Exception\UnsetKeyException;

final class PluginExtension extends Extension
{
    /**
     * @var PluginBundle
     */
    private $bundle;

    /**
     * @param PluginBundle $bundle
     */
    public function __construct(PluginBundle $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @inheritdoc
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($config, $container);

        $processedConfiguration = $this->processConfiguration($configuration, $config);

        $this->bundle->load($processedConfiguration, $container);

        foreach ($this->bundle->getPlugins() as $plugin) {
            $this->loadPlugin($container, $plugin, $processedConfiguration);
        }
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->bundle);
    }

    /**
     * @inheritdoc
     */
    public function getAlias()
    {
        return $this->bundle->getAlias();
    }

    /**
     * @param ContainerBuilder $container
     * @param PluginInterface $plugin
     * @param array $processedConfiguration The fully processed configuration values for this bundle
     */
    private function loadPlugin(ContainerBuilder $container, PluginInterface $plugin, array $processedConfiguration)
    {
        $container->addClassResource(new \ReflectionClass(get_class($plugin)));

        $pluginConfiguration = $this->pluginConfiguration($plugin, $processedConfiguration);

        $plugin->load($pluginConfiguration, $container);
    }

    /**
     * Get just the part of the configuration values that applies to the given plugin.
     *
     * @param PluginInterface $plugin
     * @param array $processedConfiguration The fully processed configuration values for this bundle
     * @return array
     */
    private function pluginConfiguration(PluginInterface $plugin, array $processedConfiguration)
    {
        if (!isset($processedConfiguration[$plugin->name()])) {
            if ($plugin->isRequireConfigurationKey()) {
                throw new UnsetKeyException(sprintf('The %s key must be set.', $plugin->name()));
            }

            return array();
        }

        return $processedConfiguration[$plugin->name()];
    }
}
