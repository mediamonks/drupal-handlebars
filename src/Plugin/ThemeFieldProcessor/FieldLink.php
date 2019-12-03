<?php

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Url;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;
use Drupal\link\LinkItemInterface;

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
    $url = $this->buildUrl($field)->toString(TRUE);
    $data = [
      'text' => $field->title,
      'url' => $url->getGeneratedUrl()
    ];
    return $data;
  }

  /**
   * Builds the \Drupal\Core\Url object for a link field item.
   *
   * @param \Drupal\link\LinkItemInterface $item
   *   The link field item being rendered.
   *
   * @return \Drupal\Core\Url
   *   A Url object.
   */
  protected function buildUrl(LinkItemInterface $item) {
    $url = $item->getUrl() ?: Url::fromRoute('<none>');

    $options = $item->options;
    $options += $url->getOptions();
    $url->setOptions($options);

    return $url;
  }

}
