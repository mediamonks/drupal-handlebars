<?php

namespace Drupal\handlebars_theme_handler\Twig;

use Drupal\handlebars_theme_handler\FilesUtility;
use Handlebars\Handlebars;
use Handlebars\Loader\FilesystemLoader;
use Drupal\handlebars_theme_handler\Templating\Renderer;

/**
 * Class RenderHandlebarsTemplate.
 *
 * @package Drupal\handlebars_theme_handler
 */
class RenderHandlebarsTemplate extends \Twig_Extension {

  /**
   * @var \Drupal\handlebars_theme_handler\Templating\Renderer
   */
  private $handlebarsRenderer;

  /**
   * Constructor
   *
   * @param \Drupal\handlebars_theme_handler\Templating\Renderer $handlebarsRenderer
   *   Handlebars rendering engine
   */
  public function __construct(Renderer $handlebarsRenderer) {
    $this->handlebarsRenderer = $handlebarsRenderer;
  }

  /**
   * {@inheritdoc}
   * This function must return the name of the extension. It must be unique.
   */
  public function getName() {
    return 'render_handlebars_template';
  }

  /**
   * In this function we can declare the extension function
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('handlebars',
        [$this, 'renderHandlebars'],
        [
          'is_safe' => ['html'],
        ]),
    ];
  }

  /**
   * Renders the given handlebars template
   *
   * @param string $templateName Name of the handlebars template
   * @param array $data Data to render
   *
   * @return string
   */
  public function renderHandlebars($templateName, array $data = []) {
    return $this->handlebarsRenderer->render($templateName, $data);
  }

}
