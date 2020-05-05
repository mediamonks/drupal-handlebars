<?php

namespace Drupal\handlebars_theme_handler\Templating;

/**
 * Interface to render handlebars templates
 */
interface RendererInterface {

  /**
   * Renders the given handlebars template.
   *
   * @param string $template
   *   Template filename e.g. "template.hbs".
   * @param array $data
   *   Data being passed to the renderer.
   *
   * @return string
   */
  public function render($template, $data = []);
}
