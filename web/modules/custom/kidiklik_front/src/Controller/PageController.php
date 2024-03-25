<?php

namespace Drupal\kidiklik_front\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class PageController.
 */
class PageController extends ControllerBase
{

  public function test()
  {

    return [
      '#type' => 'markup',
      '#markup' => ''
    ];
  }


  /**
   * return render node
   */
  public function getContent($page, $dep)
  {
    $output = "";
    if ($page) {

      $term_dep = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($dep);

      if($term_dep !== null) {
        $node = \Drupal::entityTypeManager()
        ->getStorage("node")
        ->load(current($term_dep->get("field_" . $page)->getValue())["target_id"]);

        if (is_object($node)) {
          $view = node_view($node, 'default');
          $output = drupal_render($view);
        } else {
          $output = "La page est pour le moment incompléte";
        }
      }
      
    }
    //exit;
    return $output;


  }

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function kidiklik()
  {
    $service = \Drupal::service('kidiklik.service');

    return [
      '#type' => 'markup',
      '#markup' => $this->getContent("qui_est_kidiklik", $service->getTermDepartement())
    ];
  }

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function recrute()
  {
    $service = \Drupal::service('kidiklik.service');

    return [
      '#type' => 'markup',
      '#markup' => $this->getContent("kidiklik_recrute", $service->getTermDepartement(0))
    ];
  }
  
  /**
   * Contact.
   *
   * @return string
   *   Return Hello string.
   */
  public function contact()
  {
    $page_dep = \Drupal::service('kidiklik.service')->getPageDepartement();
    $renderer = \Drupal::service("renderer");
      $build = [
        '#markup' => sprintf('<div class="jumbotron mt-4 pt-3 pb-3"><p><b class="bleu titre_h1">Coordonnées</b></p><b>Nom :</b> %s<br><b>Téléphone :</b> <a href="tel:%s">%s</a><br><b>E-mail :</b> <a href="mailto:%s">%s</a></div>',
          $page_dep->get('field_societe')->value,
          $page_dep->get('field_telephone')->value,
          $page_dep->get('field_telephone')->value,
          $page_dep->get('field_e_mail')->value,
          $page_dep->get('field_e_mail')->value
        ),
      ];
    return $build;
  }

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function annonceur()
  {
    $service = \Drupal::service('kidiklik.service');
    return [
      '#type' => 'markup',
      '#markup' => $this->getContent("devenir_annonceur", $service->getTermDepartement())
    ];
  }

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function fan()
  {
    $service = \Drupal::service('kidiklik.service');
    return [
      '#type' => 'markup',
      '#markup' => $this->getContent("fan_de_kidiklik", $service->getTermDepartement())
    ];
  }

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function partenaires()
  {
    $service = \Drupal::service('kidiklik.service');

    return [
      '#type' => 'markup',
      '#markup' => $this->getContent("partenaires", $service->getTermDepartement())
    ];
  }

}
