<?php

namespace Drupal\handlebars_theme_handler\Templating;

use Drupal\Component\PhpStorage\PhpStorageInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ExtensionList;
use Drupal\handlebars_theme_handler\FilesUtility;
use LightnCandy\LightnCandy;
use Symfony\Component\Config\FileLocator;

/**
 * Service to render handlebars templates
 */
class Renderer implements RendererInterface {

  /**
   * @var \Symfony\Component\Config\FileLocator
   */
  private $fileLocator;

  /**
   * @var \Drupal\handlebars_theme_handler\FilesUtility
   */
  private $filesUtility;

  /**
   * @var \Drupal\Component\PhpStorage\PhpStorageInterface
   */
  private $phpStorage;

  /**
   * @var array
   */
  private $templateDirectories;

  /**
   * @var array
   */
  private $helpers;

  /**
   * Constructor.
   *
   * @param \Drupal\handlebars_theme_handler\FilesUtility $filesUtility
   *   Handlebars rendering engine
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   * @param \Drupal\Core\Extension\ExtensionList $extensionList
   *   Theme extension list.
   * @param \Drupal\Component\PhpStorage\PhpStorageInterface $phpStorage
   *   Php storage for compiled templates.
   */
  public function __construct(FilesUtility $filesUtility, ConfigFactoryInterface $configFactory, ExtensionList $extensionList, PhpStorageInterface $phpStorage) {
    $this->filesUtility = $filesUtility;
    $this->phpStorage = $phpStorage;
    $defaultTheme = $configFactory->get('system.theme')->get('default');
    $themePath = dirname($extensionList->getPathname($defaultTheme));
    $templatePath = $themePath . '/templates/';
    $templateDirectories = [$templatePath];
    $this->templateDirectories = $this->filesUtility->getTemplateDirectoriesRecursive($templateDirectories);
    $this->fileLocator = new FileLocator($this->templateDirectories);
    if (empty($templateDirectories)) {
      throw new \InvalidArgumentException('No Handlebars template directories got defined in "smartive_handlebars.templating.template_directories".');
    }
  }

  /**
   * {@inheritDoc}
   */
  public function render($template, $data = []) {
    if (!$this->phpStorage->exists($template)) {
      $compiledTemplate = LightnCandy::compile(file_get_contents($this->fileLocator->locate($template)), [
        'partialresolver' => function ($cx, $name) {
          return file_get_contents($this->fileLocator->locate($name . '.hbs'));
        },
        'flags' => LightnCandy::FLAG_RUNTIMEPARTIAL | LightnCandy::FLAG_HANDLEBARSJS | LightnCandy::FLAG_EXTHELPER,
        'helpers' => $this->helpers,
      ]);
      $this->phpStorage->save($template, "<?php $compiledTemplate");
    }

    $templateFunction = include $this->phpStorage->getFullPath($template);
    return $templateFunction($data);
  }

  /**
   * Adds the given helper to the rendering service
   *
   * @param string $helperName
   *   Name of the helper.
   *
   * @param callable $callable
   *   Helper callback.
   *
   * @return void
   */
  public function addHelper($helperName, callable $callable) {
    $this->helpers[$helperName] = $callable;
  }

}
