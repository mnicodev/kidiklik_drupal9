<?php

namespace Drupal\kidiklik_admin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\views\Views;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JeuxConcoursController.
 */
class JeuxConcoursController extends ControllerBase
{

  public function getGagnants($nid)
  {
    $node = Node::load($nid);
    if ($node->get("field_gagnants_selectionnes")->value === NULL) {

      $formulaire_jeu = \Drupal::entityTypeManager()->getStorage("webform_submission")->loadByProperties([
        "entity_id" => $nid,
      ]);

      $nb_gagnants = $node->get('field_nombre_de_gagnants')->value;

      if ($nb_gagnants > count($formulaire_jeu)) {
        $nb_gagnants = count($formulaire_jeu);
      }

      $tb = array_rand($formulaire_jeu, $nb_gagnants);

      //  kint(array_rand($formulaire_jeu,$node->get('field_nombre_de_gagnants')->value));
      $csv = "";
      $line = "";

      if ($nb_gagnants === 1) {
        $line = implode(";", $formulaire_jeu[$tb]->getData());
        $csv .= $line . "\n";
      } else {
        foreach ($tb as $item) {
          $line = implode(";", $formulaire_jeu[$item]->getData());
          //$node->get("field_gagnants_selectionnes")->appendItem($line);
          $csv .= $line . "\n";
        }
      }

      $node->set("field_gagnants_selectionnes", $csv);
      $node->validate();
      $node->save();
    } else {
      $csv = $node->get("field_gagnants_selectionnes")->value;
    }

    // kint($node->get("field_gagnants_selectionnes"));exit;
    $response = new Response();
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="gagnants-' . date("Y-m-d") . '.csv"');
    $response->setContent($csv);
    return $response;


  }

}
