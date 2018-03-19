<?php

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @ThemeFieldProcessor(
 *   id = "field_link",
 *   label = @Translation("Link"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class FieldLink extends ThemeFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData(FieldItemInterface $field, $options = array()) {
    $data = [
      'text' => $field->title,
      'url' => UrlHelper::stripDangerousProtocols($field->uri)
    ];
    return $data;
  }
}
