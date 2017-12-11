<?php

namespace Drupal\mm_rest\Plugin\RestFieldProcessor;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\handlebars_theme_handler\Plugin\ThemeFieldProcessorBase;
use Drupal\mm_rest\Plugin\RestFieldProcessorBase;

/**
 * Returns the (structured) data of a field.
 *
 * @RestFieldProcessor(
 *   id = "field_entity_reference",
 *   label = @Translation("Entity reference"),
 *   field_types = {
 *     "entity_reference",
 *     "entity_reference_revisions"
 *   }
 * )
 */
class FieldEntityReference extends ThemeFieldProcessorBase {

  /**
   * {@inheritdoc}
   */
  protected function getItemData($field, $options = array()) {

    /** @var ContentEntityInterface $entity */
    $entity = $field->get('entity')->getValue();
    $data = $this->themeEntityProcessorManager->getEntityData($entity);
    return $data;

  }
}
