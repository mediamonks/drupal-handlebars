<?php

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\mm_rest\Plugin\RestFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @RestFieldProcessor(
 *   id = "field_float",
 *   label = @Translation("Float"),
 *   field_types = {
 *     "float"
 *   }
 * )
 */
class FieldFloat extends RestFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData($field, $options = array()) {
    $data = $field->getValue();
    return $data['value'];
  }
}
