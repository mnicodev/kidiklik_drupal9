<?php

namespace Drupal\kidiklik_front_publicite\Plugin\Block;

use Drupal\Core\Block\BlockBase;

//use Drupal\views\Views;
use Drupal\Core\Form\FormStateInterface;
use Drupal\kidiklik_front_publicite\PubEntity;

/**
 * Provides a 'CarreBlock' block.
 *
 * @Block(
 *  id = "carre_block",
 *  admin_label = @Translation("Carre block"),
 * )
 */
class CarreBlock extends BlockBase
{


  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity_pub = new PubEntity(95);
    return $entity_pub->build();
  }

}
