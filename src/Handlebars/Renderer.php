<?php

namespace Drupal\handlebars_theme_handler\Handlebars;

use Drupal\handlebars_theme_handler\FilesUtility;
use Drupal\handlebars_theme_handler\HandlebarsHelperServiceInterface;
use Handlebars\Cache;
use Handlebars\Handlebars;
use Handlebars\Helper;
use Handlebars\Loader\FilesystemLoader;
use LightnCandy\Runtime;
use LightnCandy\LightnCandy;

/**
 * Service to render handlebars templates
 */
class Renderer {

  /**
   * @var Handlebars
   */
  protected $handlebarsRenderingEngine;

  /**
   * @var \Drupal\handlebars_theme_handler\Handlebars\Loader
   */
  protected $newLoader;

  /**
   * @var \Drupal\handlebars_theme_handler\FilesUtility
   */
  private $filesUtility;

  /**
   * @var HandlebarsHelperServiceInterface
   */
  private $helperService;

  /**
   * Constructor.
   *
   * @param \Drupal\handlebars_theme_handler\FilesUtility $filesUtility
   *   Handlebars rendering engine
   *
   * @throws \InvalidArgumentException If no template directories got defined.
   */
  public function __construct(FilesUtility $filesUtility, HandlebarsHelperServiceInterface $helperService) {
    $this->filesUtility = $filesUtility;
    $this->helperService = $helperService;

    $defaultTheme = \Drupal::config('system.theme')->get('default');
    $templatePath = drupal_get_path('theme', $defaultTheme) . '/templates/';
    $templateDirectories = [$templatePath];

    $templateDirectories = $this->filesUtility->getTemplateDirectoriesRecursive($templateDirectories);
    if (empty($templateDirectories)) {
      throw new \InvalidArgumentException('No Handlebars template directories got defined in "smartive_handlebars.templating.template_directories".');
    }

    $loader = new FilesystemLoader($templateDirectories,
      [
        'extension' => '.hbs',
      ]
    );

    $this->newLoader = new Loader($templateDirectories,
      [
        'extension' => '.hbs',
      ]);

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
    $hbTemplateData = $this->newLoader->loadFile($template);

    $php = LightnCandy::compile($hbTemplateData, [
      'flags' => LightnCandy::FLAG_HANDLEBARSJS | LightnCandy::FLAG_RUNTIMEPARTIAL,
      'partialresolver' => function ($cx, $name) {
        if ($hbTemplateData = $this->newLoader->loadFile($name . '.hbs')) {
          return $hbTemplateData;
        }
        return "[partial (file:$name.tmpl) not found]";
      }
    ]);
    // Save the compiled PHP code into a php file
    file_put_contents('render.php', '<?php ' . $php . '?>');

    // Get the render function from the php file
    $renderer = include('render.php');

    $results = $renderer($data, ['debug' => Runtime::DEBUG_ERROR_LOG]);

    return $results;
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
