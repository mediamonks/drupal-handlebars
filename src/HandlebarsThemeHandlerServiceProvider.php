<?php
/**
 * Created by PhpStorm.
 * User: mladen
 * Date: 6/21/18
 * Time: 1:55 PM
 */

namespace Drupal\handlebars_theme_handler;


use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\handlebars_theme_handler\DependencyInjection\HandlebarsHelperPass;

class HandlebarsThemeHandlerServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new HandlebarsHelperPass());
  }
}