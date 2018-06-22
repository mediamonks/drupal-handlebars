<?php

namespace Drupal\handlebars_theme_handler;

use Drupal\handlebars_theme_handler\Helper\HelperInterface;

/**
 * Class HandlebarsHelperService
 *
 * @package Drupal\handlebars_theme_handler
 */
class HandlebarsHelperService implements HandlebarsHelperServiceInterface {

  private $helpers = [];

  /**
   * @inheritdoc
   */
  public function addHelper($id, $helper) {
    if ($helper instanceof HelperInterface) {
      $this->helpers[$id] = [$helper, 'handle'];
    }
    elseif (is_callable($helper)) {
      $this->helpers[$id] = $helper;
    }
  }

  /**
   * @inheritdoc
   */
  public function getHelperMethods() {
    return array_keys($this->helpers);
  }

  /**
   * @inheritdoc
   */
  public function getHelpers() {
    return $this->helpers;
  }
}
