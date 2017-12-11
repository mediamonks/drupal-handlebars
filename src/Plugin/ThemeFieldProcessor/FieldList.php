<?php

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\mm_rest\Plugin\RestFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @RestFieldProcessor(
 *   id = "field_list",
 *   label = @Translation("List"),
 *   field_types = {
 *     "list_integer",
 *     "list_float",
 *     "list_string"
 *   }
 * )
 */
class FieldList extends RestFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData($field, $options = array()) {
    $allowed_values = $field->getDataDefinition()->getSetting('allowed_values');
    return $allowed_values[$field->value];
  }

}
