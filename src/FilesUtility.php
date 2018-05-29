<?php

namespace Drupal\handlebars_theme_handler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;

/**
 * Class FilesUtility
 *
 * @package Drupal\handlebars_theme_handler
 */
class FilesUtility {

  /**
   * @var \Symfony\Component\Config\FileLocator
   */
  private $fileLocator;

  /**
   * @var \Symfony\Component\Finder\Finder
   */
  private $finder;

  /**
   * Constructor.
   *
   * @throws \InvalidArgumentException If no template directories got defined.
   */
  public function __construct() {
    $this->fileLocator = new FileLocator(DRUPAL_ROOT);
    $this->finder = new Finder();
  }

  /**
   * Returns all directories including their sub directories for the given
   * template resources
   *
   * @param array $templateDirectories List of directories containing
   *   handlebars templates
   *
   * @return array
   */
  public function getTemplateDirectoriesRecursive(array $templateDirectories) {
    $templateDirectoriesWithSubDirectories = [];
    $templateDirectories = $this->getTemplateDirectories($templateDirectories);

    /** @var \Symfony\Component\Finder\SplFileInfo $subDirectory */
    foreach ($this->finder->directories()
               ->in($templateDirectories) as $subDirectory) {
      $templateDirectoriesWithSubDirectories[] = $subDirectory->getRealPath();
    }

    return array_unique(array_merge($templateDirectories, $templateDirectoriesWithSubDirectories));
  }

  /**
   * Returns all directories for the given template resources
   *
   * @param array $templateDirectories List of directories containing
   *   handlebars templates
   *
   * @return array
   */
  private function getTemplateDirectories(array $templateDirectories) {
    return array_map(
      function ($templateDirectory) {
        return rtrim($this->fileLocator->locate($templateDirectory), '/');
      },
      $templateDirectories
    );
  }

  /**
   * @param $string
   * @param bool $capitalizeFirstCharacter
   * @param bool $spaceBetweenWords
   *
   * @return mixed|string
   */
  public function dashesToCamelCase($string, $capitalizeFirstCharacter = FALSE, $spaceBetweenWords = FALSE) {
    $str = str_replace('-', ' ', ucwords($string, '-'));
    if (!$spaceBetweenWords) {
      $str = str_replace(' ', '', $str);
    }
    if (!$capitalizeFirstCharacter) {
      $str = lcfirst($str);
    }

    return $str;
  }

  /**
   * @param string $filePath
   * @param string $oldText
   * @param string $newText
   */
  public function replaceTextInFile($filePath, $oldText, $newText) {
    $componentClassContent = file_get_contents($filePath);
    $FileContent = str_replace($oldText, $newText, $componentClassContent);
    file_put_contents($filePath, $FileContent);
  }

}
