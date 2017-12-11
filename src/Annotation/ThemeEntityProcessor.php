<?php

namespace Drupal\handlebars_theme_handler\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Theme Entity Processor item annotation object.
 *
 * @see \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessor
 * @see plugin_api
 *
 * @Annotation
 */
class ThemeEntityProcessor extends Plugin {

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

  /**
   * The type of entity.
   *
   * @var string
   */
  public $entity_type;

  /**
   * The bundle of entity.
   *
   * @var string
   */
  public $bundle;

  /**
   * The view_mode of entity.
   *
   * @var string
   */
  public $view_mode;

}