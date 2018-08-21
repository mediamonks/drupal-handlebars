<?php
/**
 * Created by PhpStorm.
 * User: mladen
 * Date: 10/4/17
 * Time: 4:55 PM
 */

namespace Drupal\handlebars_theme_handler\Plugin;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for ThemeFieldProcessorBase plugin plugins.
 */
abstract class ThemeFieldProcessorBase extends PluginBase implements ThemeFieldProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * The Entity processor manager.
   *
   * @var \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorManager
   */
  protected $themeEntityProcessorManager;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ThemeEntityProcessorManager $themeEntityProcessorManager, ModuleHandlerInterface $moduleHandler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->themeEntityProcessorManager = $themeEntityProcessorManager;
    $this->moduleHandler = $moduleHandler;

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
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getData($fields, $options = []) {
    $single = FALSE;
    $multiple = FALSE;
    if (isset($options['multiple']) && $options['multiple']) {
      $multiple = TRUE;
    }
    $count = $fields->count();
    $data = [];

    if ($count == 0) {
      return '';
    }

    // We want to handle both field item list as individual field items.
    if ($fields instanceof \Drupal\Core\Field\FieldItemInterface) {
      $fields = [$fields];
      $single = TRUE;
    }
    else {
      if($count == 1) {
        $single = TRUE;
      }
    }

    foreach ($fields as $field) {
      if (!$field->isEmpty()) {
        $itemData = $this->getItemData($field, $options);
        $this->moduleHandler->alter('field_preprocess_data', $itemData, $field, $options);
        $data[] = $itemData;
      }
    }

    return $single && !$multiple ? reset($data) : $data;
  }

  /**
   * Returns structured data of a single field.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $field
   * @param $options
   *
   * @return string|array
   *   Field data.
   */
  abstract protected function getItemData(FieldItemInterface $field, $options = array());

}
