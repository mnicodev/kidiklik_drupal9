<?php

namespace Drupal\kidiklik_front_publicite\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\kidiklik_front_publicite\PubEntity;

/**
 * Provides a 'RectangleBlock' block.
 *
 * @Block(
 *  id = "rectangle_block",
 *  admin_label = @Translation("Rectangle block"),
 * )
 */
class RectangleBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity_pub = new PubEntity(2578);
    return $entity_pub->build();
  }
  
}
