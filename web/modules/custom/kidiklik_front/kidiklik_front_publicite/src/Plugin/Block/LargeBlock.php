<?php

namespace Drupal\kidiklik_front_publicite\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\kidiklik_front_publicite\PubEntity;

/**
 * Provides a 'LargeBlock' block.
 *
 * @Block(
 *  id = "large_block",
 *  admin_label = @Translation("Large block"),
 * )
 */
class LargeBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity_pub = new PubEntity(98);
    return $entity_pub->build();
  }
  

}
