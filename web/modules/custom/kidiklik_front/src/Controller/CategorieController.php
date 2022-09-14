<?php

namespace Drupal\kidiklik_front\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CategorieController.
 */
class CategorieController extends ControllerBase
{

  /**
   * Get.
   *
   * @return string
   *   Return Hello string.
   */
  public function get($tid)
  {
    $categories = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties(["vid" => "rubriques_activite", "parent" => $tid, "field_departement" => get_term_departement()]);
    $tab = [];
    foreach ($categories as $cat) {
      $tab[] = $cat->getName();
    }
    return new JsonResponse(($tab));

  }

}
