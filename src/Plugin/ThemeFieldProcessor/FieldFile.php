<?php

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\mm_rest\Plugin\RestFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @RestFieldProcessor(
 *   id = "field_file",
 *   label = @Translation("File"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class FieldFile extends RestFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData($field, $options = array()) {
    $data = [
      'type' => 'file',
      'url' => file_create_url($field->entity->uri->value),
    ];
    return $data;
  }
}
