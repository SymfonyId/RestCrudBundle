<?php

namespace Symfonian\Indonesia\BundlePlugins\Tests;

use Symfonian\Indonesia\BundlePlugins\PluginBundle;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class DemoBundle extends PluginBundle
{
    public function getAlias()
    {
        return 'demo';
    }

    protected function defaultPlugins()
    {
        return array(new CorePlugin());
    }
}
