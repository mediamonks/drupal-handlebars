<?php

namespace Drupal\handlebars_theme_handler\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides the Task plugin plugin manager.
 */
class ThemeFieldProcessorManager extends DefaultPluginManager {

  protected $settings;

  /**
   * Constructor for TaskPluginManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces,
                              CacheBackendInterface $cache_backend,
                              ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ThemeFieldProcessor', $namespaces, $module_handler,
      'Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorInterface',
      'Drupal\handlebars_theme_handler\Annotation\ThemeFieldProcessor');

    $this->alterInfo('handlebars_theme_handler_field_processor_info');
    $this->setCacheBackend($cache_backend, 'handlebars_theme_handler_field_processor_plugins');
  }

  /**
   * Returns structured field data.
   *
   * @param array $field_list
   *
   * @param array $options
   *
   * @return array|string
   * @throws \Exception
   */
  public function getFieldData($field_list, $options = []) {
    $data = NULL;
    if (isset($field_list['#items']) && $field_list['#items'] instanceof FieldItemListInterface) {
      // Load plugin that matches the field
      $plugin_id = $this->getProcessor($field_list['#items']);
      /** @var \Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorInterface $processor */
      $processor = $this->createInstance($plugin_id);
      if (isset($field_list[0]['#image_style']) && !isset($options['style'])) {
        $options['style'] = $field_list[0]['#image_style'];
      }
      $data = $processor->getData($field_list['#items'], $options);
    }

    return $data;
  }

  /**
   * Returns the processor ID by field type.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $field_list
   *
   * @return string
   *    Entity processor plugin ID
   *
   * @throws \Exception
   *   When no plugin is available for the entity/bundle.
   */
  public function getProcessor(FieldItemListInterface $field_list) {
    $processor_plugin = NULL;

    // Get a plugin that matches entity type and bundle.
    $map = $this->getProcessorMap();
    $field_type = $field_list->getFieldDefinition()->getType();

    $processor_id = isset($map[$field_type]) ? $map[$field_type] : NULL;

    if (empty($processor_id)) {
      throw new \Exception(sprintf("No RestFieldProcessor plugin found for field '%s' of type '%s'.", $field_list->getName(), $field_type));
    }

    return $processor_id;
  }

  /**
   * Loads a map of all plugin IDs keyed by field type.
   *
   * @return array
   *   Plugin ID map keyed by field type.
   */
  protected function getProcessorMap() {

    if (!isset($this->plugin_map)) {
      $plugins = $this->getDefinitions();
      foreach ($plugins as $plugin_id => $plugin) {
        foreach ($plugin['field_types'] as $type) {
          $this->plugin_map[$type] = $plugin_id;
        }
      }
    }

    return $this->plugin_map;
  }

}