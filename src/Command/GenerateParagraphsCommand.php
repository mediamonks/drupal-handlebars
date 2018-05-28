<?php

namespace Drupal\handlebars_theme_handler\Command;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\Command;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\Console\Annotations\DrupalCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class GenerateParagraphsCommand.
 *
 * @DrupalCommand (
 *     extension="handlebars_theme_handler",
 *     extensionType="module"
 * )
 */
class GenerateParagraphsCommand extends Command {

  /**
   * @var \Symfony\Component\Config\FileLocator
   */
  private $fileLocator;

  /**
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $filesystem;

  /**
   * Constructor
   *
   * @throws \InvalidArgumentException If no template directories got defined.
   */
  public function __construct() {
    $this->fileLocator = new FileLocator(DRUPAL_ROOT);
    $this->filesystem = new Filesystem();
    parent::__construct();
  }


  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('handlebars_theme_handler:generate_paragraphs')
      ->setDescription($this->trans('commands.handlebars_theme_handler.generate_paragraphs.description'))
      ->setAliases(['hgp'])
      ->addArgument('module', InputArgument::REQUIRED);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    // Get theme and module.
    $defaultTheme = \Drupal::config('system.theme')->get('default');
    $module = $input->getArgument('module');

    // Get all components directories.
    $templatePath = drupal_get_path('theme', $defaultTheme) . '/templates/block';
    $templatePath = \Drupal::service('file_system')
      ->realpath($templatePath);
    if (!$templatePath) {
      $io->error(t('Folder doesn\'t exist'));
      return;
    }
    $templateDirectories = [$templatePath];
    $templateDirectories = $this->getTemplateDirectoriesRecursive($templateDirectories);

    $classTemplate = __DIR__ . '/../ThemeEntityProcessorTemplate/ParagraphBlockTemplate.php';
    $moduleComponentsPath = drupal_get_path('module', $module) . '/src/Plugin/ThemeEntityProcessor/ParagraphsBlock';
    $moduleComponentsPath = realpath($moduleComponentsPath);

    // Create component classes.
    foreach ($templateDirectories as $templateDirectory) {
      if ($templateDirectory != $templatePath) {
        $templatePathArray = explode('/', $templateDirectory);
        if (!empty($templatePathArray)) {
          $componentTemplateName = end($templatePathArray);

          // Generate Class name.
          $componentClassName = 'ParagraphsBlock' . $this->dashesToCamelCase($componentTemplateName, TRUE);
          $componentClassPath = $moduleComponentsPath . '/' . $componentClassName . '.php';
          if ($this->filesystem->exists($classTemplate)) {
            $this->filesystem->copy($classTemplate, $componentClassPath);
            $this->replaceTextInFile($componentClassPath, 'module_name', $module);
            $this->replaceTextInFile($componentClassPath, 'ParagraphBlockTemplate', $componentClassName);
            $this->replaceTextInFile($componentClassPath, 'paragraph_machine_name', str_replace('-', '_', $componentTemplateName));
            $this->replaceTextInFile($componentClassPath, 'paragraph_human_name', $this->dashesToCamelCase($componentTemplateName, TRUE, TRUE));

            // Get data from data.json file.
            $jsonData = file_get_contents($templateDirectory . '/data.json');
            $arrayData = json_decode($jsonData, TRUE);

            // Replace static text with array from JSON.
            $arrayString = preg_replace('#,(\s+|)\)#', '$1)', var_export($arrayData, true));
            $arrayString = '$variables[\'data\'] = ' . $arrayString . ';';
            $this->replaceTextInFile($componentClassPath, '// Static code goes here', $arrayString);

            $io->successLite(t('Component @component has been created', [
              '@component' => $componentClassName,
            ]));
          }
        }
      }
    }

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
  private function getTemplateDirectoriesRecursive(array $templateDirectories) {
    $templateDirectoriesWithSubDirectories = [];
    $templateDirectories = $this->getTemplateDirectories($templateDirectories);

    $finder = new Finder();

    /** @var \Symfony\Component\Finder\SplFileInfo $subDirectory */
    foreach ($finder->directories()
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
  private function dashesToCamelCase($string, $capitalizeFirstCharacter = FALSE, $spaceBetweenWords = FALSE) {
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
  private function replaceTextInFile($filePath, $oldText, $newText) {
    $componentClassContent = file_get_contents($filePath);
    $FileContent = str_replace($oldText, $newText, $componentClassContent);
    file_put_contents($filePath, $FileContent);
  }

}
