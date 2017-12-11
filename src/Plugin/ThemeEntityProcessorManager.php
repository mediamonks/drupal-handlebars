<?php

namespace Drupal\handlebars_theme_handler\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

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
      'Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorInterface',
      'Drupal\handlebars_theme_handler\Annotation\ThemeEntityProcessor');

    $this->alterInfo('handlebars_theme_handler_entity_processor_info');
    $this->setCacheBackend($cache_backend, 'handlebars_theme_handler_entity_processor_plugins');
  }

  /**
   * Returns data of an entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @param string $view_mode
   *
   * @return array|string
   * @throws \Exception
   */
  public function getEntityData(ContentEntityInterface $entity, $view_mode = 'full') {
    $data = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())
      ->view($entity, $view_mode);
    return $data;
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