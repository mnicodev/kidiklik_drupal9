<?php

namespace Drupal\kidiklik_admin\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'PubliciteCompteurBlock' block.
 *
 * @Block(
 *  id = "publicite_compteur_block",
 *  admin_label = @Translation("Publicite compteur block"),
 * )
 */
class PubliciteCompteurBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    //$build['#theme'] = 'publicite_compteur_block';
    // $build['publicite_compteur_block']['#markup'] = 'Implement PubliciteCompteurBlock.';

    return $build;
  }

}
