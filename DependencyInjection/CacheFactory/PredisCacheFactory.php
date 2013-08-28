<?php
/**
 * This file is part of KitanoCacheBundle
 *
 * (c) Kitano <contact@kitanolabs.org>
 *
 */

namespace Kitano\CacheBundle\DependencyInjection\CacheFactory;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Factory for Predis Cache
 *
 * @author Jan Brunnert <janbster@gmail.com>
 */
class PredisCacheFactory implements CacheFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $id, array $config)
    {

        $predisId = $config['backend'];

        $doctrineRedisId = sprintf('kitano_cache.doctrine_cache.%s_predis_instance', $config['name']);
        $container
            ->setDefinition($doctrineRedisId, new DefinitionDecorator('kitano_cache.doctrine_cache.predis'))
            ->addMethodCall('setRedis', array(new Reference($predisId)))
            ->setPublic(false)
        ;

        $container
            ->setDefinition($id, new DefinitionDecorator('kitano_cache.cache.doctrine_proxy'))
            ->addArgument($config['name'])
            ->addArgument(new Reference($doctrineRedisId))
            ->addArgument($config['ttl'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return 'predis';
    }

    /**
     * {@inheritDoc}
     */
    public function addConfiguration(NodeBuilder $node)
    {
        $node
            ->scalarNode('backend')->defaultNull()->end()
            ->scalarNode('ttl')->defaultNull()->end()
            ->end()
        ;
    }
}
