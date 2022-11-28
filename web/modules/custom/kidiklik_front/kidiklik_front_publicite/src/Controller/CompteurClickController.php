<?php

namespace Drupal\kidiklik_front_publicite\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node;

/**
 * Class CompteurClickController.
 */
class CompteurClickController extends ControllerBase {

  /**
   * Addclick.
   *
   * @return string
   *   Return Hello string.
   */
  public function addClick($nid) {
    if(!empty($nid)) {
      $node = Node::Load($nid);
      $cpt = $node->get('field_compteur_click')->value;
      $node->__set('field_compteur_click', ++$cpt);
      $node->save();
      return new JsonResponse(['results' => $cpt]);
    }
    
    return new JsonResponse(['results' => null]);
  }

}
