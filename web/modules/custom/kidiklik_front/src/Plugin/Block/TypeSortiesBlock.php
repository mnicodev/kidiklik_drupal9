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
    
    $rubriques = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
      "vid" => "rubriques_activite",
      "field_ref_parent" => "0",
    ]);

    if(!empty($rub = $request->get('taxonomy_term')) && 
      !empty($url =$request->server->get('SCRIPT_URL'))) {
        $kidiklik_service = \Drupal::service('kidiklik.service');

      $term_url = \Drupal::service('path_alias.manager')->getAliasByPath('/taxonomy/term/'.$rub->id());
      
      if($term_url === $url) {
        $rubriques = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
          "vid" => "rubriques_activite",
          "parent" => $rub->id(),
          'field_departement' => $kidiklik_service->getTermDepartement(),
        ]);
      }

    }

    $list = [];
    
    foreach($rubriques as $rubrique) {
      $list[] = [
        'url' => \Drupal::service('path_alias.manager')->getAliasByPath('/taxonomy/term/'.$rubrique->id()),
        'name' => $rubrique->getName()
      ];
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
        "#cache" => [
          'max-age' => 0,
        ],
      ];
    } else {
      $build = [
        '#type' => 'markup',
        '#markup' => null,
        "#cache" => [
          'max-age' => 0,
        ],
      ];
    }
   
    
    return $build;
  }

}
