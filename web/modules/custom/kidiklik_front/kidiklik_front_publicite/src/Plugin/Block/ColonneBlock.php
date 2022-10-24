<?php

namespace Drupal\kidiklik_front_publicite\Plugin\Block;

use Drupal\Core\Block\BlockBase;

use Drupal\kidiklik_front_publicite\PubEntity;

/**
 * Provides a 'ColonneBlock' block.
 *
 * @Block(
 *  id = "colonne_block",
 *  admin_label = @Translation("Colonne block"),
 * )
 */
class ColonneBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity_pub = new PubEntity(97);
    return $entity_pub->build();

  }
  
}
