<?php

namespace Drupal\handlebars_theme_handler\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Theme Field Processor item annotation object.
 *
 * @see \Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorManager
 * @see plugin_api
 *
 * @Annotation
 */
class ThemeFieldProcessor extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
