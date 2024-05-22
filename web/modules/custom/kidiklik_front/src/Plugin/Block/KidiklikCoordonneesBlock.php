<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'KidiklikCoordonneesBlock' block.
 *
 * @Block(
 *  id = "kidiklik_coordonnees_block",
 *  admin_label = @Translation("Kidiklik Coordonnees block"),
 * )
 */
class KidiklikCoordonneesBlock extends BlockBase
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
    $page_dep = \Drupal::service('kidiklik.service')->getPageDepartement();
    $renderer = \Drupal::service("renderer");
      $build = [
        '#markup' => sprintf('<div class="jumbotron mt-4 pt-3 pb-3"><p><b class="bleu titre_h1">Coordonnées</b></p><b>Nom :</b> %s<br><b>Téléphone :</b> <a href="tel:%s">%s</a><br><b>E-mail :</b> <a href="mailto:%s">%s</a></div>',
          $page_dep->get('field_societe')->value,
          $page_dep->get('field_telephone')->value,
          $page_dep->get('field_telephone')->value,
          $page_dep->get('field_e_mail')->value,
          $page_dep->get('field_e_mail')->value
        ),
      ];
    return $build;
  }

}
