<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ReseauxSociauxBlock' block.
 *
 * @Block(
 *  id = "reseaux_sociaux_block",
 *  admin_label = @Translation("Reseaux Sociaux block"),
 * )
 */
class ReseauxSociauxBlock extends BlockBase {

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
    $term_dep=\Drupal::entityTypeManager()->getStorage("taxonomy_term")->load(get_term_departement());
    
    $build['#content']=$term_dep->get('field_reseaux_sociaux2')->getValue();
    $build['#theme'] = 'reseaux_sociaux';

    return $build;
  }

}
