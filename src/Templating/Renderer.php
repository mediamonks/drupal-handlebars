<?php

namespace Drupal\handlebars_theme_handler\Templating;

use Drupal\Core\DrupalKernelInterface;
use Handlebars\Cache;
use Handlebars\Handlebars;
use Handlebars\Helper;
use Handlebars\Loader\FilesystemLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Config\FileLocator;

/**
 * Service to render handlebars templates
 */
class Renderer {

  /**
   * @var FileLocator
   */
  private $fileLocator;

  /**
   * @var Handlebars
   */
  private $handlebarsRenderingEngine;

  /**
   * Constructor
   *
   * @throws \InvalidArgumentException If no template directories got defined.
   */
  public function __construct() {
    $this->fileLocator = new FileLocator(DRUPAL_ROOT);

    $defaultTheme = \Drupal::config('system.theme')->get('default');
    $templatePath = drupal_get_path('theme', $defaultTheme) . '/templates/';
    $templateDirectories = [$templatePath];

    $templateDirectories = $this->getTemplateDirectoriesRecursive($templateDirectories);
    if (empty($templateDirectories)) {
      throw new \InvalidArgumentException('No Handlebars template directories got defined in "smartive_handlebars.templating.template_directories".');
    }

    $loader = new FilesystemLoader(
      $templateDirectories,
      [
        'extension' => '.hbs',
      ]
    );
    $this->handlebarsRenderingEngine = new Handlebars(
      [
        'loader' => $loader,
        'partials_loader' => $loader,
      ]
    );
  }

  /**
   * Renders the given handlebars template
   *
   * @param string $template Template location
   * @param array $data Data being passed to the renderer
   *
   * @return string
   */
  public function render($template, array $data = []) {
    return $this->handlebarsRenderingEngine->render($template, $data);
  }

  /**
   * Returns all directories including their sub directories for the given
   * template resources
   *
   * @param array $templateDirectories List of directories containing
   *   handlebars templates
   *
   * @return array
   */
  private function getTemplateDirectoriesRecursive(array $templateDirectories) {
    $templateDirectoriesWithSubDirectories = [];
    $templateDirectories = $this->getTemplateDirectories($templateDirectories);

    $finder = new Finder();

    /** @var SplFileInfo $subDirectory */
    foreach ($finder->directories()
               ->in($templateDirectories) as $subDirectory) {
      $templateDirectoriesWithSubDirectories[] = $subDirectory->getRealPath();
    }

    return array_unique(array_merge($templateDirectories, $templateDirectoriesWithSubDirectories));
  }

  /**
   * Returns all directories for the given template resources
   *
   * @param array $templateDirectories List of directories containing
   *   handlebars templates
   *
   * @return array
   */
  private function getTemplateDirectories(array $templateDirectories) {
    return array_map(
      function ($templateDirectory) {
        return rtrim($this->fileLocator->locate($templateDirectory), '/');
      },
      $templateDirectories
    );
  }

  /**
   * Adds the given helper to the rendering service
   *
   * @param string $helperName Name of the helper
   * @param Helper $helper Helper
   *
   * @return void
   */
  public function addHelper($helperName, Helper $helper) {
    $this->handlebarsRenderingEngine->addHelper($helperName, $helper);
  }

  /**
   * Sets the caching service
   *
   * @param Cache $cacheService
   *
   * @return void
   */
  public function setCache(Cache $cacheService) {
    $this->handlebarsRenderingEngine->setCache($cacheService);
  }
}
