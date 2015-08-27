<?php

namespace Symfonian\Indonesia\BundlePlugins\Tests;

use Symfonian\Indonesia\BundlePlugins\PluginBundle;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class BundleWithOnlyASimplePlugin extends PluginBundle
{
    public function getAlias()
    {
        return 'bundle_with_only_a_simple_plugin';
    }

    protected function defaultPlugins()
    {
        return array(
            new ASimplePlugin()
        );
    }
}
