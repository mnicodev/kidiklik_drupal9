<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'CarteBlock' block.
 *
 * @Block(
 *  id = "carte_block",
 *  admin_label = @Translation("Carte block"),
 * )
 */
class CarteBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'carte_block';
    
    return $build;
  }

}
