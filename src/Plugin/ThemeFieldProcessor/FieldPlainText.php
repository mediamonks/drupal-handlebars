<?php

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\Component\Utility\Xss;
use Drupal\mm_rest\Plugin\RestFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @RestFieldProcessor(
 *   id = "field_plain_text",
 *   label = @Translation("Plain text"),
 *   field_types = {
 *     "string",
 *     "string_long",
 *     "integer",
 *     "email"
 *   }
 * )
 */
class FieldPlainText extends RestFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData($field, $options = array()) {
    return $field->value;
  }
}
