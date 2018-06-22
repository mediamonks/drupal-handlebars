<?php
/**
 * Created by PhpStorm.
 * User: mladen
 * Date: 6/21/18
 * Time: 1:22 PM
 */

namespace Drupal\handlebars_theme_handler;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\handlebars_theme_handler\Cache\Filesystem;
use Drupal\handlebars_theme_handler\Handlebars\Loader;
use Handlebars\Loader\FilesystemLoader;
use LightnCandy\LightnCandy;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Class HandlebarsEngine
 *
 * @package Drupal\handlebars_theme_handler
 */
class HandlebarsEngine implements HandlebarsEngineInterface {

  /**
   * @var array
   */
  protected $options;

  /**
   * @var \Drupal\handlebars_theme_handler\FilesUtility
   */
  protected $filesUtility;

  /**
   * @var Filesystem
   */
  protected $cache;

  /**
   * @var FilesystemLoader
   */
  protected $loader;

  /**
   * @var bool
   */
  protected $autoReload;

  /**
   * @var bool
   */
  protected $debug;

  /**
   * @var HandlebarsHelperServiceInterface
   */
  private $helper;

  /**
   * @var string
   */
  private $defaultTheme;

  /**
   * @var ConfigFactoryInterface
   */
  private $configFactory;

  /**
   * @var array
   */
  private $templateDirectories;

  /**
   * @var \ArrayObject
   */
  private $partials;

  /**
   * HandlebarsEngine constructor.
   *
   * @param \Drupal\handlebars_theme_handler\FilesUtility $filesUtility
   * @param Loader $loader
   * @param \Drupal\handlebars_theme_handler\HandlebarsHelperServiceInterface $helper
   * @param \Drupal\handlebars_theme_handler\Cache\Filesystem $cache
   * @param ConfigFactoryInterface $configFactory
   */
  public function __construct(
    FilesUtility $filesUtility,
    Loader $loader,
    HandlebarsHelperServiceInterface $helper,
    Filesystem $cache,
    ConfigFactoryInterface $configFactory
  ) {
    $this->filesUtility = $filesUtility;
    $this->loader = $loader;
    $this->partials = $partials = new \ArrayObject();
    $this->helper = $helper;
    $this->cache = $cache;
    $this->configFactory = $configFactory;
    $this->defaultTheme = $this->configFactory->get('system.theme')
      ->get('default');
    $this->templateDirectories = $this->filesUtility->getTemplateDirectoriesRecursive($this->getThemeTemplatePath());

    $this->options = array_merge([
      'auto_reload' => NULL,
      'debug' => TRUE,
      'helpers' => $this->helper->getHelperMethods(),
      'partialresolver' => function ($cx, $name) use ($loader, &$partials) {
        if ($hbTemplateData = $this->loader->loadFile($name . '.hbs')) {
          return $hbTemplateData;
        }
        return "[partial (file:$name.hbs) not found]";
      },
    ]);
  }

  public function getTemplateDirectories() {
    return $this->templateDirectories;
  }

  /**
   * {@inheritdoc}
   */
  public function compile($name) {
    $source = $this->getLoader()->loadFile($name);
    $cacheKey = $this->getCacheFilename($name);

    $phpStr = '';
    try {
      $this->partials->exchangeArray([new FileResource($this->getLoader()->getCacheKey($name))]);
      $phpStr = LightnCandy::compile($source, $this->options);
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
    $this->cache->write($cacheKey, '<?php // '.$name.PHP_EOL.$phpStr, $this->partials->getArrayCopy());

    return $phpStr;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheFilename($name) {
    $key = $this->cache->generateKey($name);

    return !$key ? false : $key;
  }

  /**
   * {@inheritdoc}
   */
  public function getLoader() {
    return $this->loader;
  }

  /**
   * @return array
   */
  protected function getThemeTemplatePath() {
    return [drupal_get_path('theme', $this->defaultTheme) . '/templates/'];
  }

}