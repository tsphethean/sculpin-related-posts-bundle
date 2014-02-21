<?php

namespace Tsphethean\Sculpin\Bundle\RelatedPostsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Sculpin Redirect Extension.
 *
 * @author Tom Phethean <www.tsphethean.co.uk>
 */
class SculpinRelatedPostsExtension extends Extension
{
  /**
   * {@inheritdoc}
   */
  public function load(array $configs, ContainerBuilder $container)
  {
    $configuration = new Configuration;
    $config = $this->processConfiguration($configuration, $configs);

    $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.xml');
  }
}
