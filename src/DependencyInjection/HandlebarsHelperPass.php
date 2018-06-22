<?php

namespace Drupal\handlebars_theme_handler\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class HandlebarsHelperPass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   */
  public function process(ContainerBuilder $container) {
    if (!$container->has('handlebars.helper')) {
      return;
    }

    $definition = $container->findDefinition(
      'handlebars.helper'
    );

    $taggedServices = $container->findTaggedServiceIds(
      'handlebars.helper'
    );

    foreach ($taggedServices as $id => $tags) {
      $definition->addMethodCall('addHelper', [$id, new Reference($id)]);
    }
  }

}