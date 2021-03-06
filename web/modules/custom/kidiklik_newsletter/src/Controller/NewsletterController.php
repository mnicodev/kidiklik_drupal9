<?php

namespace Drupal\kidiklik_newsletter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Plugin\views\argument\Taxonomy;
use Symfony\Component\HttpFoundation\Response;
use Drupal\views\Views;
use Drupal\taxonomy\Entity\Term;

/**
 * Class NewsletterController.
 */
class NewsletterController extends ControllerBase
{

  /**
   * Newsletterformailjet.
   *
   * @return string
   *   Return Hello string.
   */
  public function newsletterForMailjet($nid, $titre)
  {
    $n = Node::Load($nid);

    $dep_id = get_term_departement();
    //$dep=get_departement();
    $view_entete = Views::getView("liste_bloc_donnees_newsletter");
    $view_entete->setDisplay("rest_export_2");
    $view_entete->setArguments([$nid]);
    $view_entete->execute();
    $json_entete = current(json_decode(\Drupal::service('renderer')->render($view_entete->render())));

    /*$view_pub = Views::getView("liste_bloc_donnees_newsletter");
    $view_pub->setDisplay("newsletter_json_pub");
    $view_pub->setArguments([$dep_id,  $json_entete->field_date_envoi, $json_entete->field_date_envoi]);


    $view_pub->execute();
    $view_pub->query->where[0]["conditions"][1]["field"] = "DATE_FORMAT(node__field_date_debut.field_date_debut_value, '%Y-%m-%d') <= :node__field_date_debut_field_date_debut_value";
    $view_pub->query->where[0]["conditions"][2]["field"] = "DATE_FORMAT(node__field_date_fin.field_date_fin_value, '%Y-%m-%d') >= :node__field_date_fin_field_date_fin_value";*/
    //$view_pub->execute();
    //$view_pub->query->where[0]["conditions"][1]["field"]="node__field_partage_departements.field_partage_departements_target_id = :node__field_partage_departements_field_partage_departements_target_id";
    //$view_pub->query->where[0]["conditions"][1]["value"]=[':node__field_partage_departements_field_partage_departements_target_id'=>(int)$dep_id];
    //$json_pub = current(json_decode(\Drupal::service('renderer')->render($view_pub->render())));

    $database = \Drupal::database();
    $query = $database->select("node_field_data", "n");
    $query->join("node__field_departement", "dep", "dep.entity_id=n.nid");
    $query->join("node__field_date_debut", "dd", "dd.entity_id=n.nid");
    $query->join("node__field_date_fin", "df", "df.entity_id=n.nid");
    $query->join("node__field_image", "img", "img.entity_id=n.nid");
    $query->leftJoin("node__field_partage_departements", "par", "par.entity_id=n.nid");

    $query->fields("n", ["nid", "title"]);
    $query->fields("dd", ["field_date_debut_value"]);
    $query->fields("df", ["field_date_fin_value"]);
    $query->fields("img", ["field_image_target_id"]);

    $query->condition("n.type", "publicite", "=");

    $orGroup = $query->orConditionGroup()
    ->condition("dep.field_departement_target_id", get_term_departement(), "=")
    ->condition("par.field_partage_departements_target_id", [get_term_departement()], "in");
    $query->condition($orGroup);

    $query->condition("dd.field_date_debut_value", $json_entete->field_date_envoi, "<=");
    $query->condition("df.field_date_fin_value", $json_entete->field_date_envoi, ">=");
    $query->orderRandom();
    $query->range(0,1);
    //kint($query->distinct()->__toString());
    $rs = current($query->execute()->fetchAll());

    $pub_img = \Drupal::entityTypeManager()->getStorage("file")->load($rs->field_image_target_id);
    
    $file = null;

    if($n->get("field_image_d_entete") !== null) {
      $entetes = $n->get("field_image_d_entete")->getValue();
      foreach($entetes as $item) {
        $entete_img_term = Term::Load($item['target_id']);
        $dept = (int)current($entete_img_term->get('field_departement')->getValue())['target_id'];
        if($dept === get_term_departement()) {
          $file = \Drupal::entityTypeManager()->getStorage("file")->load($entete_img_term->get("field_image")->first()->get("target_id")->getValue());
          break;
        }
       
      }
    }
    
    //$db=\Drupal::database();
    //$query=$db->query()

    $dep = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($dep_id);
    $blocs = $n->get('field_blocs_de_donnees')->getValue(); //\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"bloc_de_mise_en_avant","field_newsletter"=>$nid,"status"=>1]);
    $blocs_nat = [];
    foreach($blocs as $key => $item) {//kint(get_term_departement(null,'name'));
      $paragraph=\Drupal\paragraphs\Entity\Paragraph::load($item["target_id"]);
      $dept_target_id = (int)current($paragraph->get("field_departement")->getValue())['target_id'];
      $dept_bloc = (int)\Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($dept_target_id)->getName();

		  if($dept_target_id !== (int)get_term_departement()) {
        if($dept_bloc === 0) {
          $blocs_nat[] = $item;
        }
        unset($blocs[$key]);
      }
    }
    
    $blocs = array_merge($blocs,$blocs_nat);
    
    //kint(\Drupal::request());
    $entete = [
      "sujet" => $json_entete->field_sujet, //$newsletter->get("field_sujet")->value,
      "texte" => $json_entete->field_entete, //$newsletter->get("field_entete")->value,
      'image' => $file ? file_create_url($file->getFileUri()) : null,
      "bandeau_rose" => ($json_entete->field_bandeau_rose ? 1 : 0),
      "pub" => $pub_img ? file_create_url($pub_img->getFileUri()) : null,
      "dep" => $dep,
      "url" => \Drupal::request()->getRequestUri()
    ];
    $liste = [];
    //kint($blocs);exit;
    foreach ($blocs as $item) {
      $bloc = Paragraph::load($item['target_id']);

      if (is_object($bloc->get("field_image")->first())) {
        //kint($bloc->get("field_image")->first()->get("target_id")->getValue());
        $file = \Drupal::entityTypeManager()->getStorage("file")->load($bloc->get("field_image")->first()->get("target_id")->getValue());

        $url_image = file_create_url($file->getFileUri());

      } else $url_image = "";
      $liste[] = [
        "titre" => $bloc->get('field_titre')->value,
        "image" => $url_image,
        "texte" => $bloc->get("field_resume")->value,
        "lien"=>  $bloc->get("field_lien")->value,
      ];

    }
    //kint(\Drupal::request()->server->get("HTTP_HOST"));
    //kint($liste);
    //kint($newsletter->get("field_bandeau_rose")->value);
    $build = [
      '#type' => "page",
      '#theme' => 'kidiklik_newsletter',
      '#entete' => $entete,
      "#blocs" => $liste,
      "#cache" => [
        "max-age" => 0,
      ],

    ];

    //kint($output);
   // return $build;
    $output=\Drupal::service('renderer')->render($build);




    $response = new Response();
  
    $response->setContent($output);

    return $response;
  }

}
