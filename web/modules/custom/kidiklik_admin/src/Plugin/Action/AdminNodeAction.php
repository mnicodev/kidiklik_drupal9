<?php

namespace Drupal\kidiklik_admin\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Push term in front.
 *
 * @Action(
 *   id = "term_push_front",
 *   label = @Translation("Push term in front"),
 *   type = "taxonomy_term"
 * )
 */
class AdminNodeAction extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /** @var \Drupal\taxonomy\TermInterface $entity */
    

  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    

    return true;
  }

}