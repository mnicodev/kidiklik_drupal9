<?php

namespace Drupal\taxonomy_delete;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class TermDelete.
 *
 * @package Drupal\taxonomy_delete
 */
class TermDelete {
  
  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  
  /**
   * Use core services object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }
  
  /**
   * Delete terms by Vocabulary.
   *
   * @param string $vid
   *   The Vocabulary from which the Terms needs to be deleted.
   *
   * @return array
   *   An array of terms which gets deleted.
   */
  public function deleteTermByVid($vid) {
    $terms = [];
    $controller = $this->entityTypeManager->getStorage('taxonomy_term');
    $tree = $controller->loadTree($vid);
    foreach ($tree as $term) {
      $terms[] = $term->tid;
    }
    $entities = $controller->loadMultiple($terms);
    $controller->delete($entities);
    return count($terms);
  }

}
