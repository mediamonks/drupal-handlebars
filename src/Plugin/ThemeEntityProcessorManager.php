<?php

namespace Drupal\handlebars_theme_handler\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\handlebars_theme_handler\Annotation\ThemeEntityProcessor;

/**
 * Provides the Task plugin plugin manager.
 */
class ThemeEntityProcessorManager extends DefaultPluginManager {

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
    parent::__construct('Plugin/ThemeEntityProcessor', $namespaces, $module_handler,
      ThemeEntityProcessorInterface::class,
      ThemeEntityProcessor::class);

    $this->alterInfo('handlebars_theme_handler_entity_processor_info');
    $this->setCacheBackend($cache_backend, 'handlebars_theme_handler_entity_processor_plugins');
  }

  /**
   * Returns data of an entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @param array $options
   *
   * @return array|string
   * @throws \Exception
   */
  public function getEntityData(ContentEntityInterface $entity, $options = []) {

    // Load the plugin that matches the entity.
    $plugin_id = $this->getProcessor($entity, $options);
    /** @var \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorBase $processor */
    $processor = $this->createInstance($plugin_id);

    $view_mode = isset($options['view_mode']) ? $options['view_mode'] : 'default';

    $data = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())
      ->view($entity, $view_mode);
    $preparedData = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->build($data);

    $preparedData['elements'] = $preparedData;

    if (isset($options['style'])) {
      $preparedData['style'] = $options['style'];
    }

    // Get a plugin that matches entity type and bundle.
    $processor->preprocessItemData($preparedData);

    return $preparedData['data'];
  }

  /**
   * Returns the processor ID by entity type and bundle.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @param string $version
   *   API Version.
   *
   * @param array $options
   *
   * @return string Entity processor plugin ID
   * Entity processor plugin ID
   * @throws \Exception When no plugin is available for the entity/bundle.
   */
  public function getProcessor(ContentEntityInterface $entity, $options = []) {
    $processor_plugin = NULL;

    // Get a plugin that matches entity type, bundle and view mode.
    $map = $this->getProcessorMap();
    $view_mode = isset($options['view_mode']) ? $options['view_mode'] : 'default';
    $key = implode('.', [
      $entity->getEntityTypeId(),
      $entity->bundle(),
      $view_mode,
    ]);
    $processor_id = isset($map[$key]) ? $map[$key] : NULL;
    // @todo Nice: version fallback.

    if (empty($processor_id)) {
      throw new \Exception(sprintf("No EntityProcessor plugin found for entity of type '%s', bundle '%s' and view mode '%s'", $entity->getEntityTypeId(), $entity->bundle(), $view_mode));
    }

    return $processor_id;
  }

  /**
   * Loads a map of all plugin IDs keyed by entity type and bundle.
   *
   * @return array
   *   Plugin ID map keyed by {$enity_type}.{$bundle}.{view mode}.{version}.
   */
  protected function getProcessorMap() {

    if (!isset($this->plugin_map)) {
      /** @var \Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorInterface[] $plugins */
      $plugins = $this->getDefinitions();
      foreach ($plugins as $plugin_id => $plugin) {
        $key = implode('.', [
          $plugin['entity_type'],
          $plugin['bundle'],
          isset($plugin['view_mode']) ? $plugin['view_mode'] : 'default'
        ]);
        $this->plugin_map[$key] = $plugin_id;
      }
    }

    return $this->plugin_map;
  }

  /**
   * Check if active theme is front theme.
   *
   * @return bool
   *   Default theme or not.
   */
  public function isFrontTheme() {
    $activeTheme = \Drupal::service('theme.manager')->getActiveTheme()->getName();
    $defaultTheme = \Drupal::config('system.theme')->get('default');

    if ($activeTheme == $defaultTheme) {
      return TRUE;
    }

    return FALSE;
  }

}