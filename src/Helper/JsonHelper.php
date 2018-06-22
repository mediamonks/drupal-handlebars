<?php
/**
 * @author MediaMonks
 */

namespace Drupal\handlebars_theme_handler\Helper;

/**
 * Class JsonHelper
 *
 * @package Drupal\handlebars_theme_handler\Helper
 */
class JsonHelper implements HelperInterface {

  public function handle($context, $options) {
    return json_encode($context);
  }

}
