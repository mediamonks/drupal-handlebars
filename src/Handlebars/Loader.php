<?php

namespace Drupal\handlebars_theme_handler\Handlebars;

/**
 * Class Loader
 *
 * @package Drupal\handlebars_theme_handler\Templating
 */
class Loader {

  protected $baseDir;

  private $_extension = '.hbs';

  private $_prefix = '';

  private $_templates = [];

  /**
   * Loader constructor.
   *
   * @param $baseDirs
   * @param $options
   */
  public function __construct($baseDirs, $options) {
    $this->setBaseDir($baseDirs);
    $this->handleOptions($options);
  }

  /**
   * Puts directory into standardized format
   *
   * @param String $dir The directory to sanitize
   *
   * @return String
   */
  protected function sanitizeDirectory($dir) {
    return rtrim(realpath($dir), '/');
  }

  /**
   * Sets directories to load templates from
   *
   * @param string|array $baseDirs A path contain template files or array of
   *   paths
   *
   * @return void
   */
  protected function setBaseDir($baseDirs) {
    if (is_string($baseDirs)) {
      $baseDirs = [$this->sanitizeDirectory($baseDirs)];
    }
    else {
      foreach ($baseDirs as &$dir) {
        $dir = $this->sanitizeDirectory($dir);
      }
      unset($dir);
    }

    foreach ($baseDirs as $dir) {
      if (!is_dir($dir)) {
        throw new \RuntimeException(
          'FilesystemLoader baseDir must be a directory: ' . $dir
        );
      }
    }

    $this->baseDir = $baseDirs;
  }

  /**
   * Sets properties based on options
   *
   * @param array $options Array of Loader options (default: array())
   *
   * @return void
   */
  protected function handleOptions(array $options = []) {
    if (isset($options['extension'])) {
      $this->_extension = '.' . ltrim($options['extension'], '.');
    }

    if (isset($options['prefix'])) {
      $this->_prefix = $options['prefix'];
    }
  }

  /**
   * Helper function for loading a Handlebars file by name.
   *
   * @param string $name template name
   *
   * @throws \InvalidArgumentException if a template file is not found.
   * @return string Handlebars Template source
   */
  public function loadFile($name) {
    $fileName = $this->getFileName($name);

    if ($fileName === FALSE) {
      throw new \InvalidArgumentException('Template ' . $name . ' not found.');
    }

    return file_get_contents($fileName);
  }

  /**
   * @param $name
   *
   * @return string
   */
  public function getCacheKey($name) {
    return $this->getFileName($name);
  }

  /**
   * Helper function for getting a Handlebars template file name.
   *
   * @param string $name template name
   *
   * @return string Template file name
   */
  protected function getFileName($name) {
    foreach ($this->baseDir as $baseDir) {
      $fileName = $baseDir . '/';
      $fileParts = explode('/', $name);
      $file = array_pop($fileParts);

      if (substr($file, strlen($this->_prefix)) !== $this->_prefix) {
        $file = $this->_prefix . $file;
      }

      $fileParts[] = $file;
      $fileName .= implode('/', $fileParts);
      $lastCharacters = substr($fileName, 0 - strlen($this->_extension));

      if ($lastCharacters !== $this->_extension) {
        $fileName .= $this->_extension;
      }
      if (file_exists($fileName)) {
        return $fileName;
      }
    }

    return FALSE;
  }


}