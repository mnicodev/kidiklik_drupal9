<?php
namespace Drupal\kidiklik_front\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\views\Views;

/**
 * Class RobotsController.
 */
class RobotsController extends ControllerBase {
  /**
   * Generate sitemap Xml.
   *
   * @return string
   *   Return Xml list.
   */
    public function generate() {
        $request = \Drupal::request();
        $url = 'https//' . $request->getHttpHost();
        $robots_txt = file_get_contents(\Drupal::root(). '/robots.txt.base');
        $robots_txt = sprintf('%sSitemap: %s/sitemap.xml', $robots_txt, $url);

        $response = new Response($robots_txt);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
  }

}
