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

     $term_dep=\Drupal::entityTypeManager()->getStorage("taxonomy_term")->load(get_term_departement());
    
     $build['#content']=$term_dep->get('field_reseaux_sociaux2')->getValue();
     

    return $build;
  }

}
