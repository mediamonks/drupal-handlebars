<?php
/**
 * @file
 * Contains Drupal\mm_rest\Plugin\RestFieldProcessor\FieldYoutube
 */

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\mm_rest\Plugin\RestFieldProcessorBase;

/**
 * Returns the (structured) data of a youtube field.
 *
 * @RestFieldProcessor(
 *   id = "field_youtube",
 *   label = @Translation("Youtube"),
 *   field_types = {
 *     "youtube"
 *   }
 * )
 */
class FieldYoutube extends RestFieldProcessorBase {
  protected function getItemData($field, $options = array()) {
    return $field->getValue()['video_id'];
  }
}