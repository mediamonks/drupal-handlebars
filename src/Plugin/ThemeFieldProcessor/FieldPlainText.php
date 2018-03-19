<?php

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Core\Field\FieldItemInterface;
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
  protected function getItemData(FieldItemInterface $field, $options = array()) {
    return $field->value;
  }
}
