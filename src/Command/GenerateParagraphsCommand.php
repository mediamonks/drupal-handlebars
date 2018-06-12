<?php

namespace Drupal\handlebars_theme_handler\Command;

use Drupal\handlebars_theme_handler\FilesUtility;
use Drupal\paragraphs\Entity\ParagraphsType;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\Command;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\Console\Annotations\DrupalCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

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
   * @var \Drupal\handlebars_theme_handler\FilesUtility
   */
  private $filesUtility;

  /**
   * Constructor.
   *
   * @param \Drupal\handlebars_theme_handler\FilesUtility $filesUtility
   *   Handlebars rendering engine
   *
   * @throws \InvalidArgumentException If no template directories got defined.
   */
  public function __construct(FilesUtility $filesUtility) {
    $this->fileLocator = new FileLocator(DRUPAL_ROOT);
    $this->filesystem = new Filesystem();
    $this->filesUtility = $filesUtility;
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
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
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
    $templateDirectories = $this->filesUtility->getTemplateDirectoriesRecursive($templateDirectories);

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
          $componentClassName = 'ParagraphsBlock' . $this->filesUtility->dashesToCamelCase($componentTemplateName, TRUE);
          $componentClassPath = $moduleComponentsPath . '/' . $componentClassName . '.php';
          if ($this->filesystem->exists($classTemplate)) {
            // Copy file.
            $this->filesystem->copy($classTemplate, $componentClassPath);

            // Add module name in namespace, paragraph ID, label.
            $this->filesUtility->replaceTextInFile($componentClassPath, 'module_name', $module);
            $this->filesUtility->replaceTextInFile($componentClassPath, 'ParagraphBlockTemplate', $componentClassName);
            $id = str_replace('-', '_', $componentTemplateName);
            $this->filesUtility->replaceTextInFile($componentClassPath, 'paragraph_machine_name', $id);
            $label = $this->filesUtility->dashesToCamelCase($componentTemplateName, TRUE, TRUE);
            $this->filesUtility->replaceTextInFile($componentClassPath, 'paragraph_human_name', $label);

            // Get data from data.yaml file.
            if (file_exists($templateDirectory . '/data.yaml')) {
              $arrayData = Yaml::parseFile($templateDirectory . '/data.yaml');

              // Replace static text with array from JSON.
              $arrayString = preg_replace('#,(\s+|)\)#', '$1)', var_export($arrayData, true));
              $arrayString = '$variables[\'data\'] = ' . $arrayString . ';';
              $this->filesUtility->replaceTextInFile($componentClassPath, '// Static code goes here', $arrayString);

              // Create Paragraph types.
              $paragraph_type = ParagraphsType::load($id);
              if (!$paragraph_type instanceof ParagraphsType) {
                $paragraph_type = ParagraphsType::create([
                  'id' => $id,
                  'label' => $label,
                ]);
                $paragraph_type->save();
              }

              // Display successful message.
              $io->successLite(t('Component @component has been created', [
                '@component' => $componentClassName,
              ]));
            }
          }
        }
      }
    }
  }

}
