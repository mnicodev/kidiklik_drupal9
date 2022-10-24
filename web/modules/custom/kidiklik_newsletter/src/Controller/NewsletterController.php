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
    $entetes = $n->get('field_bloc_entete')->getValue() ?? null;
    $paragraph_entete = null;
    if (!empty($entetes)) {
      foreach ($entetes as $entete) {
        $paragraph_entete = \Drupal\paragraphs\Entity\Paragraph::load($entete['target_id']);
        $dep_term = current($paragraph_entete->get('field_departement')->getValue())['target_id'];
        if ((int)$dep_term === (int)$dep_id) {
          break;
        }
      }
    }
    if (!empty($paragraph_entete)) {
	    $img_url = null;
	    if((bool)$paragraph_entete->get('field_image')->getValue() === true) {
		    $img = \Drupal::entityTypeManager()->getStorage("file")->load(current($paragraph_entete->get('field_image')->getValue())['target_id']);
		    $url_img=$img->url();
	    }
      $json_entete = [
        'field_bandeau_rose' => $paragraph_entete->get('field_bandeau_rose')->value,
        'id' => $paragraph_entete->id(),
        'field_description' => $paragraph_entete->get('field_description')->value,
        'field_image' => $url_img,
        'field_sujet' => $paragraph_entete->get('field_sujet')->value
	];
    }

    $database = \Drupal::database();
    $query = $database->select("node_field_data", "n");
    $query->join("node__field_departement", "dep", "dep.entity_id=n.nid");
    $query->join("node__field_date_debut", "dd", "dd.entity_id=n.nid");
    $query->join("node__field_date_fin", "df", "df.entity_id=n.nid");
    $query->join("node__field_image", "img", "img.entity_id=n.nid");
    $query->join("node__field_format", "format", "format.entity_id=n.nid");
    $query->join("node__field_url", "url", "url.entity_id=n.nid");
    $query->leftJoin("node__field_partage_departements", "par", "par.entity_id=n.nid");

    $query->fields("n", ["nid", "title", "status"]);
    $query->fields("dd", ["field_date_debut_value"]);
    $query->fields("df", ["field_date_fin_value"]);
    $query->fields("img", ["field_image_target_id"]);
    $query->fields("format", ["field_format_target_id"]);
    $query->fields("url", ["field_url_uri"]);

    $query->condition("n.type", "publicite", "=");
    $query->condition("n.status", 1, "=");
    $query->condition("format.field_format_target_id", 106, "=");

    $orGroup = $query->orConditionGroup()
      ->condition("dep.field_departement_target_id", get_term_departement(), "=")
      ->condition("par.field_partage_departements_target_id", [get_term_departement()], "in");
    $query->condition($orGroup);
    //$date=explode('-',$n->get('field_date_envoi')->value);
    $date_envoi = $n->get('field_date_envoi')->value; //date('Y-m-d',mktime(0,0,0,$date_envoi[1],$date_envoi[2],$date_envoi[0]));
    $query->condition("dd.field_date_debut_value",$date_envoi, "<=");
    $query->condition("df.field_date_fin_value", $date_envoi, ">=");
    $query->orderRandom();
    $query->range(0, 1);
    //kint($query->distinct()->__toString());
    $rs = current($query->execute()->fetchAll());
    $pub_img = \Drupal::entityTypeManager()->getStorage("file")->load($rs->field_image_target_id);
    $pub_url = null;
    if(!empty($rs->field_url_uri)) {
	    $pub_url = $rs->field_url_uri;
    }
    $file = null;
    if ((bool)$n->get("field_image_d_entete")->getValue() === true) {
      $entetes = $n->get("field_image_d_entete")->getValue();
      foreach ($entetes as $item) {
        $entete_img_term = Term::Load($item['target_id']);
        $dept = (int)current($entete_img_term->get('field_departement')->getValue())['target_id'];
        if ($dept === (int)get_term_departement()) {
          $file = \Drupal::entityTypeManager()->getStorage("file")->load($entete_img_term->get("field_image")->first()->get("target_id")->getValue());
          break;
        }

      }
    }

    $dep = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($dep_id);
    $blocs = $n->get('field_blocs_de_donnees')->getValue(); //\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"bloc_de_mise_en_avant","field_newsletter"=>$nid,"status"=>1]);
    $blocs_nat = [];
    foreach ($blocs as $key => $item) {//kint(get_term_departement(null,'name'));
      if (!empty($item["target_id"])) {
        $paragraph = \Drupal\paragraphs\Entity\Paragraph::load($item["target_id"]);
        if ((bool)($paragraph->get("field_departement")->getValue()) === true) {
          $dept_target_id = (int)current($paragraph->get("field_departement")->getValue())['target_id'];
	  if(!empty($paragraph->get("field_nid_bloc")->value)) {
	  	$partage_nat = Node::Load($paragraph->get("field_nid_bloc")->value)->get('field_partage_departements')->getValue();
		$dep_part = [];
		foreach($partage_nat as $part) {
			
			$dep_part[] = Term::Load($part['target_id'])->getName();
		}
	 }
          $dept_bloc = (int)\Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($dept_target_id)->getName();
          if ($dept_target_id !== (int)get_term_departement()) {

            if (($dept_bloc === 0 && in_array(get_departement(), $dep_part)) || ($dept_bloc === 0 && !count($dep_part))) {
              		$blocs_nat[] = $item;
	   }
            unset($blocs[$key]);
          }
        }
      }
    }
    
    //$blocs = array_merge($blocs,$blocs_nat);
    $entete = [
      "sujet" => htmlspecialchars_decode($json_entete['field_sujet'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401) ?? null, //$newsletter->get("field_sujet")->value,
      "texte" => htmlspecialchars_decode($json_entete['field_description']) ?? null, //$newsletter->get("field_entete")->value,
      'image' => $json_entete['field_image'],
      "bandeau_rose" => $json_entete['field_bandeau_rose'],
      "pub" => $pub_img ? file_create_url($pub_img->getFileUri()) : null,
      "pub_url" => $pub_url,
      "dep" => $dep,
      "url" => \Drupal::request()->getRequestUri()
    ];
    $liste = [];
    foreach ($blocs as $item) {
      $bloc = Paragraph::load($item['target_id']);

      if (is_object($bloc->get("field_image")->first())) {
        //kint($bloc->get("field_image")->first()->get("target_id")->getValue());
        $file = \Drupal::entityTypeManager()->getStorage("file")->load($bloc->get("field_image")->first()->get("target_id")->getValue());

        $url_image = file_create_url($file->getFileUri());

      } else {
        $url_image = "";
      }
      if (!empty($bloc->get('field_titre')->value)) {
        $liste[] = [
          "titre" => $bloc->get('field_titre')->value,
          "image" => $url_image,
          "texte" => $bloc->get("field_resume")->value,
          "lien" => $bloc->get("field_lien")->value,
        ];
      }

    }
    $liste_nat = [];
    foreach ($blocs_nat as $item) {
      $bloc = Paragraph::load($item['target_id']);

      if (is_object($bloc->get("field_image")->first())) {
        //kint($bloc->get("field_image")->first()->get("target_id")->getValue());
        $file = \Drupal::entityTypeManager()->getStorage("file")->load($bloc->get("field_image")->first()->get("target_id")->getValue());

        $url_image = file_create_url($file->getFileUri());

      } else {
        $url_image = "";
      }
      if (!empty($bloc->get('field_titre')->value)) {
        $liste_nat[] = [
          "titre" => $bloc->get('field_titre')->value,
          "image" => $url_image,
          "texte" => $bloc->get("field_resume")->value,
          "lien" => $bloc->get("field_lien")->value,
        ];
      }

    }

    $globalSettings = \Drupal::service("settings");
    $entete['www'] = 'www.';
    if($globalSettings->get("environment") === 'dev') {
      $entete['www'] = '';
    }
    $entete['domaine'] = $globalSettings->get("domain_name");
	  $entete['url'] = 'https://'.$entete['dep']->getName().'.'.$globalSettings->get("domain_name").'/newsletter/'.$n->id().$n->url();
    $entete['nom_dep'] = current($entete['dep']->get('field_nom')->getValue())['value'];
    $build = [
      '#type' => "page",
      '#theme' => 'kidiklik_newsletter',
      '#entete' => $entete,
      "#blocs" => $liste,
      "#blocs_nat" => $liste_nat,
      "#cache" => [
        "max-age" => 0,
      ],

    ];
    //kint($entete);
    // return $build;
    $output = \Drupal::service('renderer')->render($build);


    $response = new Response();

    $response->setContent($output);

    return $response;
  }

}
