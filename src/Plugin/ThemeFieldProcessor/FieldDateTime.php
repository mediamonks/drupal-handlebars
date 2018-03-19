<?php

namespace Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessor;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;
use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager;
use Drupal\Core\Field\FieldItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the (structured) data of a field.
 *
 * @ThemeFieldProcessor(
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
   * @param \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager $entity_processor
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ThemeEntityProcessorManager $entity_processor, DateFormatterInterface $date_formatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_processor);
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
      $container->get('plugin.manager.handlebars_theme_handler_entity_processor'),
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
  protected function getItemData(FieldItemInterface $field, $options = array()) {
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
