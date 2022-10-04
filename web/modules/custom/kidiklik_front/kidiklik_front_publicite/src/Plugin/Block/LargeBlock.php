<?php

namespace Drupal\kidiklik_front_publicite\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'LargeBlock' block.
 *
 * @Block(
 *  id = "large_block",
 *  admin_label = @Translation("Large block"),
 * )
 */
class LargeBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
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
    (node__field_format.field_format_target_id = '98')) AND (
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
            DATE_FORMAT(node__field_date_debut.field_date_debut_value, '%Y-%m-%d') <= DATE_FORMAT('" . date("Y-m-d") . "', '%Y-%m-%d')
        ) AND (
            DATE_FORMAT(node__field_date_fin.field_date_fin_value, '%Y-%m-%d') >= DATE_FORMAT('" . date("Y-m-d") . "', '%Y-%m-%d')
        )
    ) or (
        (node__field_nombre_affichage_possible.field_nombre_affichage_possible_value > '0') AND ((nc.totalcount<=nb_aff_poss.field_nombre_affichage_possible_value))
    )
        )
)
ORDER BY random_field ASC
LIMIT 1 OFFSET 0";

    // ksm($query.$where);
    $db = \Drupal\Core\Database\Database::getConnection();
    $rs = $db->query($query . $where)->fetchAll();
    $result = [];

    $style = ImageStyle::load("crop_850_212");
kint($style);
    foreach ($rs as $item) {
      $node = Node::load($item->nid);
      $fid = current($node->get("field_image")->getValue())["target_id"];
      if (!empty($fid)) {
        $img = \Drupal::entityManager()->getStorage('file')->load($fid);
        //$result["img"] = file_create_url(($img->getFileUri()));
        $result["img"] = $style->buildUrl($img->getFileUri());
      } else {
        $img_save = $node->get("field_image_save")->getValue();
        $result["img"] = 'https://www.kidiklik.fr/images/vendos/' . current($img_save)['value'];
      }
      $result["url"] = current($node->get("field_url")->getValue())["uri"];
      $result["nid"] = $node->id();
    }
    $result["class"] = "large";
kint($result);

    $path_stat = \Drupal::request()->getBasePath() . "/" . drupal_get_path("module", "statistics") . "/statistics.php";


    $build = [
      "#theme" => 'publicite_block',
      "#content" => $result,
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

    return $build;
  }

}
