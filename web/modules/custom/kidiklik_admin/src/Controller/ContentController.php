<?php

namespace Drupal\kidiklik_admin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\views\Views;
use Drupal\node\Entity\Node;

/**
 * Class ContentController.
 */
class ContentController extends ControllerBase
{

  /**
   * Redirect.
   *
   * @return string
   *   Return Hello string.
   */
  public function pages()
  {
    $dep = get_term_departement();
    $response = new RedirectResponse("/taxonomy/term/" . $dep . "/edit");
    return $response;
  }

  /**
   * lance une action sur un noeud en ajax
   * @return Ajax response
   *
   */
  public function action($action, $node_id)
  {

    $node = Node::load($node_id);
    if ($action == "status") {
      if ($node->get("status")->value) {
        $node->set("status", false);
        $css = ["background" => "red"];
      } else {
        $node->set("status", true);
        $css = ["background" => "green"];
      }
    }

    $node->save();

    $response = new AjaxResponse();
    $Selector = '.node-status-' . $node_id;


    $response->addCommand(new CssCommand($Selector, $css));

    return $response;
  }

  public function getVilles($cp)
  {
    $database = \Drupal::database();
    $query = $database->query("select * from villes where code_postal='" . $cp . "'");
    $villes = $query->fetchAll();
    $tab = [];
    foreach ($villes as $ville) {
      $tab[] = ["name" => $ville->commune, "tid" => $ville->id_ville];
    }
    //kint(json_encode($tab));
    return new JsonResponse(($tab));
  }

  public function manageContent() {
    /*$query = \Drupal::entityQuery('node')
      ->condition('type', 'agenda')
      //->condition('status', 1)
      ->condition('field_date.entity.field_date_de_fin', '2022-01-01', '<');

    $nids = $query->execute();

    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);  */
    $offset = filter_input(INPUT_GET, 'offset') ?? 0;
      $database = \Drupal::database();

      $query = $database->select('node_field_data', 'n');
        $query->fields('n', ['nid', 'title']);
        $query->condition('n.type', 'agenda');

        $query->join('node__field_date', 'nb', 'nb.entity_id = n.nid');
        $query->join('paragraph__field_date_de_fin', 'p', 'p.entity_id = nb.field_date_target_id');
        $query->addField('p', 'field_date_de_fin_value', 'date_fin');
        $query->condition('p.field_date_de_fin_value', '2022-01-01', '<');
        $query->groupBy('n.nid');
        $query->groupBy('n.title');
        $query->orderBy('p.field_date_de_fin_value');
        $query->range(0,30);

        $result = $query->execute()->fetchAll();
        foreach($result as $row) {
            $node = node::Load($row->nid);
            $node->delete();
            printf("%s - %s - %s<br>", $row->date_fin, $row->nid, $row->title);
        }

        echo "<script>
            setTimeout(() => {
                document.location.href='?offset=".($offset+10)."'
            }, 300)
            </script>
            ";

        exit;
  }

}
