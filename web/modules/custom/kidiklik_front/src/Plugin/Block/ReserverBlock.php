<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ReserverBlock' block.
 *
 * @Block(
 *  id = "sorties_bouton_block",
 *  admin_label = @Translation("Reserver block"),
 * )
 */
class ReserverBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      "#cache" => [
        "max-age"=>0,
        "contexts"=>[],
        "tags"=>[],
      ],
    ];
    
    $build['#theme'] = 'reserver_bouton';
    $node = \Drupal::request()->get('node');

    if(empty($node)) {  
      return null;
    }

    ksm($node->get('field_type_de_reservation')->getValue());
    

    return $build;
  }

}
