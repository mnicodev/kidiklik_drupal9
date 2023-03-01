<?php

namespace Drupal\kidiklik_front\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SearchGeoController.
 */
class SearchGeoController extends ControllerBase
{

  /**
   * searchCity.
   *
   * @return string
   *   Return Hello string.
   */
  public function searchCity()
  {
    $search = \Drupal::request()->query->get('search');
    $database = \Drupal::database();

    if ($search === null || $search === '') {
      $sql = 'select * from villes where commune<>"" group by commune order by commune limit 0, 100';
    } else {
      $sql = "select * from villes where code_postal like :cp or commune like :commune group by commune order by commune";
    }

    $query = $database->query($sql, [
      ':cp' => '%' . $search,
      ':commune' => '%' . $search . '%'
    ]);
    $villes = $query->fetchAll();
    $output = [];
    foreach ($villes as $ville) {
      $dep = str_pad((int)($ville->code_postal / 1000), 2, '0', STR_PAD_LEFT);
      
      $output[] = [
        'id' => $dep,
        'text' => $ville->commune,
      ];
    }


    return new JsonResponse(['results' => $output]);
  }

  /**
   * searchDep.
   *
   * @return string
   *   Return Hello string.
   */
  public function searchDep()
  {

    $output = [];
    $search = \drupal::request()->query->get('search');
    if (empty($search)) {
      $deps = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties(["vid" => "departement"]);
      foreach ($deps as $dep) {
        if ($dep->getName() !== "0") {
          $val = (int)$dep->getName();
          if ($val < 10) $val = "0$val";
          $output[] = [
            'id' => $val,
            'text' => $dep->get('field_nom')->value . " - " . $val,
          ];
        }
      }
    } else {

      $query = \Drupal::entityQuery('taxonomy_term')
        ->condition('field_nom', '%' . $search . '%', 'like')
        ->condition('vid', 'departement')
        ->execute();
      foreach ($query as $item) {
        $dep = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load((int)$item);
        $output[] = [
          'id' => $dep->getName(),
          'text' => $dep->get('field_nom')->value
        ];
      }
      //kint($query);

    }


    return new JsonResponse(['results' => $output]);
  }
}
