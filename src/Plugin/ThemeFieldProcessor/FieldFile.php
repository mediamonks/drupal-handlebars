<?php

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @ThemeFieldProcessor(
 *   id = "field_file",
 *   label = @Translation("File"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class FieldFile extends ThemeFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData(FieldItemInterface $field, $options = array()) {
    $data = [
      'type' => 'file',
      'url' => file_create_url($field->entity->uri->value),
    ];
    return $data;
  }
}
