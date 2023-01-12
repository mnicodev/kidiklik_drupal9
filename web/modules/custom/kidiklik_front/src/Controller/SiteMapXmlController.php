<?php

namespace Drupal\kidiklik_front\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\views\Views;

/**
 * Class SiteMapXmlController.
 */
class SiteMapXmlController extends ControllerBase {

  /**
   * Generate sitemap Xml.
   *
   * @return string
   *   Return Xml list.
   */
  public function generate() {
    $list = [];
    $dept = get_term_departement();
    $url = \Drupal::Request()->server->get('HTTP_HOST');
    $protocol = \Drupal::Request()->server->get('HTTPS') ? 'https://' : 'http://';
    $url = $protocol.$url;

    
    //kint($view->getQuery());
    /**
     * récupération des rubriques
     */
    $rubriques = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties(["status" => 1, "vid" => "rubriques_activite", "parent" => 0]);
    foreach($rubriques as $rubrique) {
      $list[] = [
        'loc' => sprintf('%s%s',$url,$rubrique->url()),
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'weekly',
        'priority' => 1,
      ];
    }

    $database = \Drupal::database();
    /**
     * on liste les reportages en premier
     */
    
    $view = Views::getView('sitemap_test');
    $view->setDisplay('sitemapxml_reportage');
    $reportages = json_decode($view->executeDisplay()['#markup']->__toString());
    if(!empty($reportages)) {
      foreach($reportages as $reportage) {
        $list[] = [
          'loc' => $reportage->view_node,
          'lastmod' => date('Y-m-d'),
          'changefreq' => 'weekly',
          'priority' => 1,
        ];
      }
    }
    

    $view = Views::getView('sitemap_test');
    $view->setDisplay('sitemapxml_agenda');
    $agendas = json_decode($view->executeDisplay()['#markup']->__toString());
    if(!empty($agenda)) {
      foreach($agendas as $agenda) {
        $list[] = [
          'loc' => $agenda->view_node,
          'lastmod' => date('Y-m-d'),
          'changefreq' => 'weekly',
          'priority' => 1,
        ];
      }
    }
    

    $view = Views::getView('sitemap_test');
    $view->setDisplay('sitemapxml_article');
    $articles = json_decode($view->executeDisplay()['#markup']->__toString());
    if(!empty($articles)) {
      foreach($articles as $article) {
        $list[] = [
          'loc' => $article->view_node,
          'lastmod' => date('Y-m-d'),
          'changefreq' => 'weekly',
          'priority' => 1,
        ];
      }
    }
    
    $view = Views::getView('sitemap_test');
    $view->setDisplay('sitemapxml_activite');
    $activites = json_decode($view->executeDisplay()['#markup']->__toString());
    if(!empty($activites)) {
      foreach($activites as $activite) {
        $list[] = [
          'loc' => $activite->view_node,
          'lastmod' => date('Y-m-d'),
          'changefreq' => 'weekly',
          'priority' => 1,
        ];
      }
    }

    
    $build = [
      '#theme' => 'sitemap_xml',
      '#content' => $list
    ];
    $output = \Drupal::service('renderer')->render($build);
    $response = new Response($output, Response::HTTP_OK, [
      'Content-type' => 'application/xml; charset=utf-8',
      'X-Robots-Tag' => 'noindex, follow',
    ]);
    return $response;
  }

}
