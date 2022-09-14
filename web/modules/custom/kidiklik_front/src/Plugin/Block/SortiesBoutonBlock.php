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
class SortiesBoutonBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $build = [
      "#cache" => [
        "max-age" => 0,
        "contexts" => [],
        "tags" => [],
      ],
    ];

    $build['#theme'] = 'sortie_moment_bouton';
    $node = \Drupal::request()->get('node');

    if (!empty($node)) {
      if ($node->getType() === 'activite') {
        $build['#ref_act'] = $node->id();
      } else {
        $build['#ref_adh'] = current($node->get('field_adherent')->getValue())['target_id'];
      }
    }

    if (empty($build['#ref_act']) || empty($build['#ref_adh'])) {
      return null;
    }

    return $build;
  }

}
