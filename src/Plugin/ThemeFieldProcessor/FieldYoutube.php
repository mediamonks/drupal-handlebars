<?php
/**
 * @file
 * Contains Drupal\mm_rest\Plugin\RestFieldProcessor\FieldYoutube
 */

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;

/**
 * Returns the (structured) data of a youtube field.
 *
 * @ThemeFieldProcessor(
 *   id = "field_youtube",
 *   label = @Translation("Youtube"),
 *   field_types = {
 *     "youtube"
 *   }
 * )
 */
class FieldYoutube extends ThemeFieldProcessorBase {
  protected function getItemData(FieldItemInterface $field, $options = []) {
    return $field->getValue()['video_id'];
  }
}