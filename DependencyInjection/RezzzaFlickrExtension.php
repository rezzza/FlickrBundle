<?php

namespace Rezzza\FlickrBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * RezzzaFlickrExtension
 *
 * @uses Extension
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RezzzaFlickrExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $configs);

        foreach ($config['clients'] as $name => $client) {
            $metadata  = new Definition('Rezzza\Flickr\Metadata', array($client['key'], $client['secret']));
            $client    = new Definition('Rezzza\Flickr\ApiFactory', array($metadata, new Definition($client['http_adapter'])));

            $container->setDefinition(sprintf('rezzza.flickr.client.%s', $name), $client);
        }

        $container->setAlias('rezzza.flickr.client', sprintf('rezzza.flickr.client.%s', $config['default_client']));
    }
}
