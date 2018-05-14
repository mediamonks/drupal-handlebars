<?php

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the (structured) data of a field.
 *
 * @ThemeFieldProcessor(
 *   id = "field_image",
 *   label = @Translation("Image"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class FieldImage extends ThemeFieldProcessorBase {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager $entity_processor
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ThemeEntityProcessorManager $entity_processor, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_processor);

    $this->entityProcessor = $entity_processor;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.handlebars_theme_handler_entity_processor'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getItemData(FieldItemInterface $field, $options = array()) {

    if (isset($options['style'])) {
      $url = $this->getStyledImageUrl($field, $options['style']);
    }
    else {
      $url = file_create_url($field->entity->uri->value);
    }

    $data = [
      'url' => $url,
      'alt' => Xss::filter($field->alt),
    ];

    return $data;
  }

  /**
   * Returns the URL of a styled image.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $field
   *   The image field.
   * @param string $style
   *   The image style
   *
   * @return string
   *   The URL of the styled image. Or the URL of the original image if the
   *   style is unknown. This will generate the requested styled image.
   */
  protected function getStyledImageUrl(FieldItemInterface $field, $style) {
    $original_url = $field->entity->uri->value;

    $style = ImageStyle::load($style);
    if ($style) {
      $url = $style->buildUrl($original_url);
    }
    else {
      $url = file_create_url($original_url);
    }

    return $url;
  }

}
