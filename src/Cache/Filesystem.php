<?php
/**
 * @author MediaMonks
 */

namespace Drupal\handlebars_theme_handler\Cache;

use Symfony\Component\Config\ConfigCache;

class Filesystem {

  /**
   * @var string
   */
  private $directory;

  /**
   * @var bool
   */
  private $debug;

  /**
   * @param $directory string The root cache directory
   * @param bool $debug
   */
  public function __construct($directory, $debug = FALSE) {
    $this->directory = $directory;
    $this->debug = $debug;
  }

  public function generateKey($name) {
    $hash = hash('sha256', $name);

    return $this->directory . '/' . $hash[0] . $hash[1] . '/' . $hash . '.php';
  }

  public function isFresh($key) {
    $cache = new ConfigCache($key, $this->debug);
    return $cache->isFresh();
  }

  /**
   * @param $key
   *
   * @return mixed
   */
  public function load($key) {
    return @include $key;
  }

  /**
   * @param $key
   * @param $content
   * @param $resources
   */
  public function write($key, $content, $resources) {
    $cache = new ConfigCache($key, $this->debug);
    $cache->write($content, $resources);

    return;
  }
}
