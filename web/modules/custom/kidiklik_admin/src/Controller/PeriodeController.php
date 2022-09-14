<?php

namespace Drupal\kidiklik_admin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PeriodeController.
 */
class PeriodeController extends ControllerBase
{

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function content()
  {
    $build = [
      "#theme" => "periode_pub",
      "#pub" => "hello world",
    ];

    return new Response(drupal_render($build));
  }

}
