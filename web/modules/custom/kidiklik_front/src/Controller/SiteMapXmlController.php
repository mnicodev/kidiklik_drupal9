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
        $protocol = (\Drupal::Request()->server->get('HTTPS') || \Drupal::Request()->server->get('HTTP_X_FORWARDED_PROTO') == 'https') ? 'https://' : 'http://';
        $url = $protocol.$url;

        /**
         * récupération des rubriques
         */
        $entity_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
        $query_result = $entity_storage->getQuery()
        ->condition('vid', 'rubriques_activite')
        ->condition('field_ref_parent', '0')
        ->condition('status', '1')
        ->sort('field_poids_sitemap', 'ASC')
        ->execute();
        $rubriques = $entity_storage->loadMultiple($query_result);

        $rubriques_mere = [];
        $liste_rubriques_enfant = [];
        $rubriques = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties(["status" => 1, "vid" => "rubriques_activite", "parent" => 0]);

	 foreach($rubriques as $rubrique) {
          //if((bool)$rubrique->get('field_poids_sitemap')->getValue() === false) continue;
          $rubriques_mere[] = [
            'loc' => sprintf('%s%s',$url,$rubrique->url()),
            'lastmod' => date('Y-m-d'),
            'changefreq' => 'weekly',
            'priority' => 1,
          ];
          //kint($rubrique->Id());
          $rubriques_enfants = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
            "status" => 1, 
            "vid" => "rubriques_activite", 
            "field_departement" => $dept,
            "parent" => $rubrique->Id()
          ]);
          foreach($rubriques_enfants as $item)  {
            $liste_rubriques_enfant[] = [
              'loc' => sprintf('%s%s',$url,$item->url()),
              'lastmod' => date('Y-m-d'),
              'changefreq' => 'weekly',
              'priority' => 1,
            ];
          }
          //kint($rubriques_enfants);
        }

        //$database = \Drupal::database();
        /**
         * on liste les reportages en premier
         */
        
        $articles = [];
        $view = Views::getView('sitemap_xml');
        $view->setDisplay('sitemapxml_article');
        $reportages = json_decode($view->executeDisplay()['#markup']->__toString());
        
        if(!empty($reportages)) {
          foreach($reportages as $reportage) {
            $articles[] = [
              'loc' => $reportage->view_node,
              'lastmod' =>$reportage->changed,// date('Y-m-d'),
              'changefreq' => 'weekly',
              'priority' => 1,
            ];
          }
        }

        $liste_activites = [];
        $view = Views::getView('sitemap_xml');
        $view->setDisplay('sitemapxml_activite');
        $activites = json_decode($view->executeDisplay()['#markup']->__toString());
        if(!empty($activites)) {
          foreach($activites as $activite) {
            $liste_activites[] = [
              'loc' => $activite->view_node,
              'lastmod' => $activite->changed, //date('Y-m-d'),
              'changefreq' => 'weekly',
              'priority' => 1,
            ];
          }
        }

	$racine = [
		'loc' => $url,
		'priority' => 1
	]; 

        /*$view = Views::getView('sitemap_xml');
        $view->setDisplay('sitemapxml_agenda');
        $agendas = json_decode($view->executeDisplay()['#markup']->__toString());
        if(!empty($agenda)) {
          foreach($agendas as $agenda) {
            $list[] = [
              'loc' => $agenda->view_node,
              'lastmod' => $agenda->changed, //date('Y-m-d'),
              'changefreq' => 'weekly',
              'priority' => 1,
            ];
          }
        }*/
        

        /*$view = Views::getView('sitemap_xml');
        $view->setDisplay('sitemapxml_article');
        $articles = json_decode($view->executeDisplay()['#markup']->__toString());
        if(!empty($articles)) {
          foreach($articles as $article) {
            $list[] = [
              'loc' => $article->view_node,
              'lastmod' => $article->changed, //date('Y-m-d'),
              'changefreq' => 'weekly',
              'priority' => 1,
            ];
          }
        }*/
        
        
//        $list = array_merge($rubriques_mere,$liste_rubriques_enfant, $articles, $liste_activites);
        $list = array_merge([['loc'=>$url,'lastmod'=>null,'changefreq'=>'weekly','priority'=>1]],$articles, $liste_activites, $rubriques_mere, $liste_rubriques_enfant);
        
        
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
