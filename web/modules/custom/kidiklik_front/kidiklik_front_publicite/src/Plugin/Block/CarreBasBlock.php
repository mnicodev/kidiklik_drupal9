<?php

namespace Drupal\kidiklik_front_publicite\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\kidiklik_front_publicite\PubEntity;

/**
 * Provides a 'CarreBasBlock' block.
 *
 * @Block(
 *  id = "carre_bas_block",
 *  admin_label = @Translation("Carre bas block"),
 * )
 */
class CarreBasBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity_pub = new PubEntity(957);
    return $entity_pub->build();
  }
  

}
