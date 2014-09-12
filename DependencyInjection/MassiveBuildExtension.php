<?php

namespace Massive\Bundle\BuildBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MassiveBuildExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('massive_build.command.build.class', $config['command_class']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config['targets'] as $target => $config) {
            $dependencies = array_keys($config['dependencies']);
            $targetDefinition = new Definition('Massive\Bundle\BuildBundle\Builder\VirtualBuilder', array(
                $target, $dependencies
            ));
            $targetDefinition->addTag('massive_build.builder');
            $container->setDefinition('massive_build.builder.virtual.' . $target, $targetDefinition);
        }
    }
}
