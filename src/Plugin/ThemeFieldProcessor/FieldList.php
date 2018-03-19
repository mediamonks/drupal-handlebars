<?php

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @ThemeFieldProcessor(
 *   id = "field_list",
 *   label = @Translation("List"),
 *   field_types = {
 *     "list_integer",
 *     "list_float",
 *     "list_string"
 *   }
 * )
 */
class FieldList extends ThemeFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData(FieldItemInterface $field, $options = []) {
    $allowed_values = $field->getDataDefinition()->getSetting('allowed_values');
    return $allowed_values[$field->value];
  }

}
