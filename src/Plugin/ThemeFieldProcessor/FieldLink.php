<?php

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\Component\Utility\UrlHelper;
use Drupal\mm_rest\Plugin\RestFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @RestFieldProcessor(
 *   id = "field_link",
 *   label = @Translation("Link"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class FieldLink extends RestFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData($field, $options = array()) {
    $data = [
      'caption' => $field->title,
      'url' => UrlHelper::stripDangerousProtocols($field->uri)
    ];
    return $data;
  }
}
