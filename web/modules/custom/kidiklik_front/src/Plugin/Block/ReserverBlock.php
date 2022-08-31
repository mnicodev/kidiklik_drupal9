<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ReserverBlock' block.
 *
 * @Block(
 *  id = "reserver_block",
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
    $id_resa = current($node->get('field_type_de_reservation')->getValue())['target_id'] ?? null;
    if(empty($id_resa)) {
	    return null;
    }
    $resa = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($id_resa);
    $build['#type_resa'] = $resa->getName();
    $build['#tel'] = $node->get('field_telephone')->value ?? null;
    $build['#email'] = $node->get('field_email')->value ?? null;
    $build['#lien'] = $node->get('field_lien')->value ?? null;
    return $build;
  }

}
