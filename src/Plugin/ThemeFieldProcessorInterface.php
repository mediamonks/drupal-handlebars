<?php

namespace Drupal\handlebars_theme_handler\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Rest Field Processor plugins.
 */
interface ThemeFieldProcessorInterface extends PluginInspectionInterface {

  /**
   * Returns structured data of a field.
   *
   * @param \Drupal\Core\Field\FieldItemInterface|\Drupal\Core\Field\FieldItemListInterface $fields
   * @param $options
   *
   * @return array|string
   *   Returns array of data when $fields is a field item list with multiple
   *   field items. When $fields is a single field instance or a field list with
   *   a single item, the return value is a string.
   */
  public function getData($fields, $options = []);

}
