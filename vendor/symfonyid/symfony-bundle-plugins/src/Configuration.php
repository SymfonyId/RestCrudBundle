<?php

namespace Symfonian\Indonesia\BundlePlugins;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->bundle->getAlias());
        $this->bundle->addConfiguration($rootNode);

        foreach ($this->bundle->getPlugins() as $plugin) {
            $pluginNode = $rootNode->children()->arrayNode($plugin->name());
            $plugin->addConfiguration($pluginNode);
        }

        return $treeBuilder;
    }
}
