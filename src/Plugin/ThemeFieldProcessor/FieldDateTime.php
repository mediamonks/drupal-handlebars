<?php

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;
use Drupal\mm_rest\CacheableMetaDataCollectorInterface;
use Drupal\mm_rest\Plugin\RestFieldProcessorBase;
use Drupal\mm_rest\Plugin\RestEntityProcessorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the (structured) data of a field.
 *
 * @RestFieldProcessor(
 *   id = "field_datetime",
 *   label = @Translation("Date time"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class FieldDateTime extends ThemeFieldProcessorBase {

  /**
   * The Date Formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\mm_rest\Plugin\RestEntityProcessorManager $entity_processor
   * @param \Drupal\mm_rest\CacheableMetaDataCollectorInterface $cacheable_metadata_collector
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RestEntityProcessorManager $entity_processor, CacheableMetaDataCollectorInterface $cacheable_metadata_collector, DateFormatterInterface $date_formatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_processor, $cacheable_metadata_collector);
    $this->dateFormatter = $date_formatter;
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
      $container->get('date.formatter')
    );
  }

  /**
   * Formats a date, using a date type or a custom date format string.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $field
   * @param array $options
   *   See options detail in \Drupal\Core\Datetime\DateFormatterInterface::format()
   *
   * @return string
   *   Date formatted.
   */
  protected function getItemData($field, $options = array()) {
    $options += [
      'type' => 'medium',
      'format' => '',
      'timezone' => NULL,
      'langcode' => NULL,
    ];

    $timestamp = is_string($field->value) ? strtotime($field->value) : $field->value;
    $date = $this->dateFormatter->format($timestamp, $options['type'], $options['format'], $options['timezone'], $options['langcode']);

    return $date;
  }
}
