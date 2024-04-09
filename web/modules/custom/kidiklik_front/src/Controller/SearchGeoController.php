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
   * searchGeoLoc.
   *
   * @return string
   *   Return Hello string.
   */
  public function searchGeoLoc(){
    $search = \Drupal::request()->query->get('search');
    $database = \Drupal::database();
    $departement = \Drupal::service('kidiklik.service')->getDepartement();

    $search2 = str_replace(' ','-', $search);
    if ($search === null || $search === '') {
      $sql = 'select * from villes where commune<>"" order by commune limit 0, 100';
    } else {
      $sql = "select * from villes where code_postal like :cp or commune like :commune or commune like :commune2  order by commune";
    }

    $query = $database->query($sql, [
      ':cp' =>  $search.'%',
      ':commune' => $search . '%',
      ':commune2' => $search2 . '%',
      //':dep' => $departement,
    ]);

    $villes = $query->fetchAll();
    $output = [];
    if(is_numeric($search)) {
      $output[] = [
        'id' => sprintf('d_%s',$search),
        'text' => $search
      ];
    }
    foreach ($villes as $ville) {
      $dep = str_pad((int)($ville->code_postal / 1000), 2, '0', STR_PAD_LEFT);

      $output[] = [
        'key' => $dep,
        'id' => sprintf('id_%s',$ville->id_ville),
        'text' => sprintf('%s (%s)',$ville->commune,$dep),
      ];
    }


    return new JsonResponse(['results' => $output]);
  }


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

    usort($output, function ($a, $b) {
      return strcmp($a['id'], $b['id']);
    });

    return new JsonResponse(['results' => $output]);
  }
}
