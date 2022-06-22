<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'RechercheGeoBlock' block.
 *
 * @Block(
 *  id = "recherche_geo_block",
 *  admin_label = @Translation("Recherche geo block"),
 * )
 */
class RechercheGeoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'recherche_geo_block';
     
    return $build;
  }

}
