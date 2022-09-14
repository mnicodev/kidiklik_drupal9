<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'RechercheBlock' block.
 *
 * @Block(
 *  id = "recherche_block",
 *  admin_label = @Translation("Recherche block"),
 * )
 */
class RechercheBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $form = \Drupal::formBuilder()->getForm('Drupal\kidiklik_front\Form\RechercheForm');
    return $form;

  }

}
