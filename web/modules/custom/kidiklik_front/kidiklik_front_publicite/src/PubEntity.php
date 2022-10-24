<?php

namespace Drupal\kidiklik_front_publicite;

use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Form\FormStateInterface;

class PubEntity {

    public $type;
    public $result;
    public $style;

    public function __construct($type) {
        if(!empty($type)) {
            $this->type = $type;
        } else {
            throw new Exception('un type est necessaire');
        }
        $this->result = [];
        switch($type) {
            case 95:
            case 957:
                $this->style = ImageStyle::load("crop_1_1");
                $this->result['class'] = 'carre';
                break;
            case 98:
                $this->style = ImageStyle::load("crop_850_212");
                $this->result['class'] = 'large';
                break;
            case 97:
                $this->style = ImageStyle::load("crop_1_2");
                $this->result['class'] = 'carre';
                break;
            case 2578:
                $this->style = ImageStyle::load("crop_2_1");
                $this->result['class'] = 'large';
                break;
        }
        
    }


    public function query() {
        $query = "SELECT node_field_data.nid AS nid,  RAND() AS random_field
        FROM node_field_data
        LEFT JOIN node__field_date ON node_field_data.nid = node__field_date.entity_id
        
        INNER JOIN node__field_format node__field_format ON node_field_data.nid = node__field_format.entity_id AND node__field_format.deleted = '0'
        LEFT JOIN node__field_departement node__field_departement ON node_field_data.nid = node__field_departement.entity_id AND node__field_departement.deleted = '0'
        LEFT JOIN node__field_tous_les_sites node__field_tous_les_sites ON node_field_data.nid = node__field_tous_les_sites.entity_id AND node__field_tous_les_sites.deleted = '0'
        LEFT JOIN node__field_partage_departements node__field_partage_departements ON node_field_data.nid = node__field_partage_departements.entity_id AND node__field_partage_departements.deleted = '0'
        LEFT JOIN node__field_nombre_affichage_possible node__field_nombre_affichage_possible ON node_field_data.nid = node__field_nombre_affichage_possible.entity_id
        LEFT JOIN node__field_date_debut node__field_date_debut ON node_field_data.nid = node__field_date_debut.entity_id AND node__field_date_debut.deleted = '0' AND (node__field_date_debut.langcode = node_field_data.langcode OR node__field_date_debut.bundle = 'activite')
        LEFT JOIN node__field_date_fin node__field_date_fin ON node_field_data.nid = node__field_date_fin.entity_id AND node__field_date_fin.deleted = '0' AND (node__field_date_fin.langcode = node_field_data.langcode OR node__field_date_fin.bundle = 'activite')
        left JOIN node_counter nc ON node_field_data.nid = nc.nid
        left JOIN node__field_nombre_affichage_possible nb_aff_poss ON node_field_data.nid = nb_aff_poss.entity_id";
        
        $where = " WHERE (
            (node__field_format.field_format_target_id = '".$this->type."')) AND (
            (
                (node_field_data.status = '1') AND
                (node_field_data.type IN ('publicite'))
            )
            AND (
                (node__field_tous_les_sites.field_tous_les_sites_value = '1') OR
                (node__field_partage_departements.field_partage_departements_target_id = '" . get_term_departement() . "')
            ) AND (
                (
                (
                    DATE_FORMAT(node__field_date_debut.field_date_debut_value, '%Y-%m-%d') <= DATE_FORMAT('" . get_date() . "', '%Y-%m-%d')
                ) AND (
                    DATE_FORMAT(node__field_date_fin.field_date_fin_value, '%Y-%m-%d') >= DATE_FORMAT('" . get_date() . "', '%Y-%m-%d')
                )
            ) or (
                (node__field_nombre_affichage_possible.field_nombre_affichage_possible_value > '0') AND ((nc.totalcount<=nb_aff_poss.field_nombre_affichage_possible_value))
            )
                )
        )
        ORDER BY random_field ASC
        LIMIT 1 OFFSET 0";

        $db = \Drupal\Core\Database\Database::getConnection();
        return $db->query($query . $where)->fetchAll();
    
    }

    public function build()  {
        $rs = $this->query();
        
        foreach ($rs as $item) {
            $node = Node::load($item->nid);
            //$node->get("field_image")->first()->getValue()
            $fid = current($node->get("field_image")->getValue())["target_id"];
            if (!empty($fid)) {
              $img = \Drupal::entityManager()->getStorage('file')->load($fid);
      
              $this->result["img"] = file_create_url(($img->getFileUri()));
            } else {
              $img_save = $node->get("field_image_save")->getValue();
              $this->result["img"] = 'https://www.kidiklik.fr/images/vendos/' . current($img_save)['value'];
            }
      
            //$result["img"]=$style->buildUrl($img->uri->value);
            $this->result["url"] = current($node->get("field_url")->getValue())["uri"];
            $this->result["nid"] = $node->id();
          }
          $path_stat = \Drupal::request()->getBasePath() . "/" . drupal_get_path("module", "statistics") . "/statistics.php";
        return [
            "#theme" => 'publicite_block',
            "#content" => $this->result,
            "#path_stat" => $path_stat,
            /*"#attached" => [
                "library" => ["kidiklik_front/kidiklik_front_publicite/kidiklik_front_publicite.actions"],
            ],*/
            "#cache" => [
              "max-age" => 0,
              "contexts" => [],
              "tags" => [],
            ],
            //"#markup"
          ];
    }

}