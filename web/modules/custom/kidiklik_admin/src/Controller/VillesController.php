<?php

namespace Drupal\kidiklik_admin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\kidiklik_base\kidiklikEntity;

/**
 * Class VillesController.
 */
class VillesController extends ControllerBase
{

  public function get()
  {

  }

  /**
   * Getgps.
   *
   * @return string
   *   Return Hello string.
   */
  public function getGPS($ville)
  {
    $database = \Drupal::database();
    $query = $database->query("select * from villes where commune='" . $ville . "'");
    $rs = current($query->fetchAll());

    return new JsonResponse($rs);
  }

  /**
   * GetByCp.
   *
   * @return string
   *   Return Hello string.
   */
  public function getByCp($cp)
  {
    $database = \Drupal::database();
    $query = $database->query("select * from villes where code_postal='" . $cp . "'");
    $rs = ($query->fetchAll());

    foreach ($rs as $item) {
      $tab[] = [
        "tid" => $item->id_ville,
        "name" => $item->commune,
      ];
    }

    return new JsonResponse($tab);

  }

}
