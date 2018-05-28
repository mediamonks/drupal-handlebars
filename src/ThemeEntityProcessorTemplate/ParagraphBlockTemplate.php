<?php

namespace Drupal\module_name\Plugin\ThemeEntityProcessor\ParagraphsBlock;

use Drupal\handlebars_theme_handler\Plugin\ThemeEntityProcessorBase;

/**
 * Returns the structured data of an entity.
 *
 * @ThemeEntityProcessor(
 *   id = "paragraph_machine_name",
 *   label = @Translation("paragraph_human_name"),
 *   entity_type = "paragraph",
 *   bundle = "paragraph_machine_name",
 *   view_mode = "default"
 * )
 */
class ParagraphBlockTemplate extends ThemeEntityProcessorBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessItemData(&$variables) {
    // Static code goes here
  }

}
