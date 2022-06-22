<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FacebookBlock' block.
 *
 * @Block(
 *  id = "facebook_block",
 *  admin_label = @Translation("Facebook block"),
 * )
 */
class FacebookBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'facebook_block';

     $build['#dept'] = get_departement();

    return $build;
  }

}
