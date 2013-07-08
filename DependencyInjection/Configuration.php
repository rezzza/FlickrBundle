<?php

namespace Rezzza\FlickrBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @uses ConfigurationInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('rezzza_flickr');
        $rootNode = $this->validateRootNode($rootNode);

        $rootNode
            ->children()
                ->scalarNode('key')->end()
                ->scalarNode('secret')->end()
                ->scalarNode('http_adapter')->defaultValue('Rezzza\Flickr\Http\GuzzleAdapter')->end()
                ->scalarNode('default_client')->defaultValue('default')->end()
                ->arrayNode('clients')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('key')->isRequired()->end()
                            ->scalarNode('secret')->isRequired()->end()
                            ->scalarNode('http_adapter')->defaultValue('Rezzza\Flickr\Http\GuzzleAdapter')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * validateRootNode 
     *
     * @param NodeParentInterface $rootNode rootNode
     * 
     * @return NodeParentInteface
     */
    public function validateRootNode(NodeParentInterface $rootNode)
    {
        $rootNode
            ->beforeNormalization()
                ->ifTrue(function($v) {
                    return !empty($v['key']);
                })
                ->then(function($v) {
                    if (isset($v['default'])) {
                        throw new \LogicException('Default node already defined');
                    }

                    if (!isset($v['secret'])) {
                        throw new \LogicException('Secret has to be defined');
                    }

                    $v['clients']['default'] = array(
                        'key'          => $v['key'],
                        'secret'       => $v['secret'],
                    );

                    if (isset($v['http_adapter'])) {
                        $v['clients']['default']['http_adapter'] = $v['http_adapter'];
                    }

                    unset($v['key'], $v['secret']);

                    return $v;
                })
            ->end()
            ->validate()
                ->ifTrue(function($v) {
                    return !isset($v['clients'][$v['default_client']]);
                })
                ->thenInvalid(sprintf("The default client you've defined ('%s') does not exists.", $v['default_client']))
            ->end();

        return $rootNode;
    }
}
