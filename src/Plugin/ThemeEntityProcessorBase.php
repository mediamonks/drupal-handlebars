<?php
/**
 * Created by PhpStorm.
 * User: mladen
 * Date: 10/4/17
 * Time: 4:55 PM
 */

namespace Drupal\handlebars_theme_handler\Plugin;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for ThemeFieldProcessorBase plugin plugins.
 */
abstract class ThemeEntityProcessorBase extends PluginBase implements ThemeEntityProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * The Entity processor manager.
   *
   * @var \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager
   */
  protected $themeEntityProcessorManager;

  /**
   * The Entity processor manager.
   *
   * @var \Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorManager
   */
  protected $themeFieldProcessorManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ThemeEntityProcessorManager $themeEntityProcessorManager, ThemeFieldProcessorManager $themeFieldProcessorManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->themeEntityProcessorManager = $themeEntityProcessorManager;
    $this->themeFieldProcessorManager = $themeFieldProcessorManager;
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
      $container->get('plugin.manager.handlebars_theme_handler_field_processor')
    );
  }

  /**
   * Make sure that it is always an array.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $field
   *   Field item list.
   * @param array $options
   *   Options.
   *
   * @return array|string
   *   Array of items.
   */
  public function getItems(FieldItemListInterface $field, array $options = []) {
    // It have to be always an array.
    $items = $this->themeFieldProcessorManager->getFieldData($field, $options);

    /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface $fieldStorage */
    $fieldStorage = $field->getFieldDefinition()->getFieldStorageDefinition();
    if (empty($items) && $fieldStorage->isMultiple()) {
      $items = [];
    }

    if (count($field) == 1) {
      $items = [$items];
    }

    return $items;
  }

  /**
   * Returns structured data of a single field.
   *
   * @param array $variables
   * @throws \Exception
   */
  abstract public function preprocessItemData(&$variables);

}
