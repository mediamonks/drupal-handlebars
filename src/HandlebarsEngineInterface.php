<?php

namespace Drupal\handlebars_theme_handler;

/**
 * Interface HandlebarsEngineInterface
 *
 * @package Drupal\handlebars_theme_handler
 */
interface HandlebarsEngineInterface {

  /**
   * @return mixed
   */
  public function getTemplateDirectories();

  /**
   * @param $name
   *
   * @return mixed
   *
   * @throws \Exception
   */
  public function compile($name);

  /**
   * @param $name
   *
   * @return bool|mixed|string
   */
  public function getCacheFilename($name);

  /**
   * @return \Drupal\handlebars_theme_handler\Handlebars\Loader|\Handlebars\Loader\FilesystemLoader|mixed
   */
  public function getLoader();

}