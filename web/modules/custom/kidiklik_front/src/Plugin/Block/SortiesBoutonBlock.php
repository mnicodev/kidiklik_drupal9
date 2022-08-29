<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'SortiesBoutonBlock' block.
 *
 * @Block(
 *  id = "sorties_bouton_block",
 *  admin_label = @Translation("Sorties bouton block"),
 * )
 */
class SortiesBoutonBlock extends BlockBase {

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
    
    $build['#theme'] = 'sortie_moment_bouton';
    $node = \Drupal::request()->get('node');

    if(!empty($node)) {  
      if($node->getType() === 'activite') {
          $build['#ref_act'] = $node->id();
        } else {
            $build['#ref_act'] = current($node->get('field_activite')->getValue())['target_id'];
      }
    }
    
    if(empty($build['#ref_act'])) {
      return null;
    }

    $liste = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(
      [
        'type' => 'agenda',
        'field_activite_lie' => $build['#ref_act']
      ]      
    );
    if(count($liste) === 0) {
      return null;
    }

    return $build;
  }

}
