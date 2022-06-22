<?php

namespace Drupal\kidiklik_front\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class PartenairesController.
 */
class PartenairesController extends ControllerBase {

  /**
   * Getcontent.
   *
   * @return string
   *   Return Hello string.
   */
  public function getContent() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: getContent')
    ];
  }

}
