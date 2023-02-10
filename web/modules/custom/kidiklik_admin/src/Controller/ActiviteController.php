<?php

namespace Drupal\kidiklik_admin\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ActiviteController.
 */
class ActiviteController extends ControllerBase {

  /**
   * Getadresse.
   *
   * @return string
   *   Return Hello string.
   */
  public function getAdresse($nid) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: getAdresse')
    ];
  }

}
