<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'TypeSortiesBlock' block.
 *
 * @Block(
 *  id = "type_sorties_block",
 *  admin_label = @Translation("Type sorties block"),
 * )
 */
class TypeSortiesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $request = \Drupal::request();
    $uri = $request->getRequestUri();
    $rubriques = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
      "vid" => "rubriques_activite",
      "field_ref_parent" => "0",
    ]);
    $kidiklik_service = \Drupal::service('kidiklik.service');
    $rub = $request->get('taxonomy_term');
    $url =$request->server->get('SCRIPT_URL');
    if(!empty($rub)) {

      $term_url = \Drupal::service('path_alias.manager')->getAliasByPath('/taxonomy/term/'.$rub->id());

      $rubriques = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
        "vid" => "rubriques_activite",
        "parent" => $rub->id(),
        'field_departement' => $kidiklik_service->getTermDepartement(),
      ]);

      if(empty($rubriques)) {
        $rubriques = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
          "vid" => "rubriques_activite",
          "parent" => current($rub->get('parent')->getValue())['target_id'],
          'field_departement' => $kidiklik_service->getTermDepartement(),
        ]);
      }

    }
    $list = [];

    foreach($rubriques as $rubrique) {
      if($request->getPathInfo()==='/') {

      $sous_rub = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
        "vid" => "rubriques_activite",
        "parent" => $rubrique->Id(),
        "field_departement" => $kidiklik_service->getTermDepartement(),
      ]);
      } else {
        $sous_rub = true;
      }

      if(!empty($sous_rub)) {
        $list[] = [
          'url' => \Drupal::service('path_alias.manager')->getAliasByPath('/taxonomy/term/'.$rubrique->id()),
          'name' => $rubrique->getName()
        ];
      }

    }
    usort($list, function ($a, $b)
    {
        return strcmp($a["name"], $b["name"]);
    });

    if(!empty($list)) {
      $list = array_merge([['url'=>null,'name'=> $this->t('Type de sorties')]],$list);

      $build = [
        "#theme" => 'type_sorties_block',
        "#content" => $list,
        '#origin' => $uri,
        "#cache" => [
          'max-age' => 0,
        ],
        '#attached' => [
          'library' => [
            'kidiklik_front/kidiklik_front.type_sortie',
          ],
        ]
      ];
    } else {
      $build = [
        '#type' => 'markup',
        '#markup' => null,
        "#cache" => [
          'max-age' => 0,
        ],
        '#attached' => [
          'library' => [
            'kidiklik_front/kidiklik_front.type_sortie',
          ],
        ]
      ];
    }


    return $build;
  }

}
