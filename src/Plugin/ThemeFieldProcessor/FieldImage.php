<?php

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\mm_rest\CacheableMetaDataCollectorInterface;
use Drupal\mm_rest\Plugin\RestEntityProcessorManager;
use Drupal\mm_rest\Plugin\RestFieldProcessorBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the (structured) data of a field.
 *
 * @RestFieldProcessor(
 *   id = "field_image",
 *   label = @Translation("Image"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class FieldImage extends RestFieldProcessorBase {

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
   * @param \Drupal\mm_rest\Plugin\RestEntityProcessorManager $entity_processor
   * @param \Drupal\mm_rest\CacheableMetaDataCollectorInterface $cacheable_metadata_collector
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RestEntityProcessorManager $entity_processor, CacheableMetaDataCollectorInterface $cacheable_metadata_collector, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_processor, $cacheable_metadata_collector);

    $this->entityProcessor = $entity_processor;
    $this->cacheabilityCollector = $cacheable_metadata_collector;
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
      $container->get('plugin.manager.mm_rest_entity_processor'),
      $container->get('mm_rest.cacheable_metadata_collector'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getItemData($field, $options = array()) {

    $file_entity = $field->entity;
    if (isset($options['style'])) {
      $style_config = $this->entityTypeManager->getStorage('image_style')->load($options['style']);
      $this->cacheabilityCollector->addCacheableDependency($style_config);
      $url = $this->getStyledImageUrl($field, $options['style']);
    }
    else {
      $url = file_create_url($file_entity->uri->value);
    }
    $this->cacheabilityCollector->addCacheableDependency($file_entity);

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
  protected function getStyledImageUrl($field, $style) {
    $original_url = $field->get('entity')->uri->value;

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
