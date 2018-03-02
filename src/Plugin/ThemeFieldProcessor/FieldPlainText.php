<?php

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Component\Utility\Xss;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @ThemeFieldProcessor(
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
class FieldPlainText extends ThemeFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData($field, $options = array()) {
    return $field->value;
  }
}
