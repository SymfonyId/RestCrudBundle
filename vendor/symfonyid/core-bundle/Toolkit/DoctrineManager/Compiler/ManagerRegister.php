<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ManagerRegister implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('symfonyid.core.manager_factory')) {
            return;
        }

        $definition = $container->findDefinition(
            'symfonyid.core.manager_factory'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'symfonyid.manager'
        );
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addManager',
                array(new Reference($id))
            );
        }
    }
}
