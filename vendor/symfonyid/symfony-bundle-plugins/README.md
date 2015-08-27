# Symfony Bundle Plugins

By Originally Matthias Noback

This package helps you create extensible bundles, by introducing a plugin
system for bundles. Each bundle plugin can define its own services and
configuration. This basically makes your bundles conform to the open/closed
principle.

## Setup

Install this library in your project by running

    composer require ihsanudin/symfony-bundle-plugins

## Example

First, your bundle should extend `PluginBundle`. You need to implement
the `getAlias` method. It should return the name of your bundle's
configuration key (as it will be used in `config.yml` for instance).

```php
use Symfonian\Indonesia\BundlePlugins\PluginBundle;

class DemoBundle extends PluginBundle
{
    protected function getAlias()
    {
        return 'demo';
    }
}
```

Each plugin for the bundle should implement `PluginBundle`:

```php
use Symfonian\Indonesia\BundlePlugins\PluginBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class FooPlugin implements PluginBundle
{
    public function name()
    {
        return 'foo';
    }

    public function load(
        array $pluginConfiguration,
        ContainerBuilder $container
    ) {
        // load specific service definitions for this plugin,
        // just like you would do in a bundle extension

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('foo.yml');

        // $pluginConfiguration contains just the values that are relevant
        // for this plugin
    }

    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        // add plugin-specific configuration nodes,
        // just like you would do in a bundle extension

        $pluginNode
            ->children()
                ->scalarNode('foo')
                ->isRequired()
            ->end();
    }
}
```

When instantiating this bundle in your `AppKernel` class, you can provide any
number of `PluginBundle` instances:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            ...,
            new DemoBundle(array(new FooPlugin()))
        );
    }
}
```

If some of the plugins are required, just introduce a `CorePlugin` and make
sure it is always registered by overriding your bundle's
`alwaysRegisteredPlugins()` method:

```php
class DemoBundle
{
    ...

    protected function alwaysRegisteredPlugins()
    {
        return array(new CorePlugin());
    }
}
```

## Register compiler passes

When a bundle plugin needs to register a compiler pass, it can do so in its
`build()` method.

```php
class FooPlugin implements PluginBundle
{
    ...

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(...);
    }
}
```

## Booting a plugin

Whenever the main bundle is booted, plugins are allowed to do some runtime
initialization as well. They can do this in their `boot()` method. At that
time, the fully initialized service container is available:

```php
class FooPlugin implements PluginBundle
{
    ...

    public function boot(ContainerInterface $container)
    {
        // runtime initialization (will run when the kernel itself is
        // booted)
    }
}
```

## Simple plugins

If your plugin is quite simple (i.e. only needs a `load()` method), just make
the plugin class extend `SimplePluginBundle` which contains stub
implementations for the interface methods that you won't need.

## Thanks

To [@dennisdegreef](https://github.com/dennisdegreef) for reviving the test
suite of this project.
