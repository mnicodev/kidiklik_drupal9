<?php

namespace Drupal\kidiklik_front\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RechercheController.
 */
class RechercheController extends ControllerBase
{


  /**
   * return render node
   */
  public function content()
  {
    $sql = "SELECT DISTINCT paragraphs_item_field_data_node__field_date__paragraph__field_date_de_debut.field_date_de_debut_value AS paragraphs_item_field_data_node__field_date__paragraph__fiel,
		node_field_data.nid AS nid, paragraphs_item_field_data_node__field_date.id AS paragraphs_item_field_data_node__field_date_id,
		paragraphs_item_field_data_node__field_filtres.id AS paragraphs_item_field_data_node__field_filtres_id
FROM
{node_field_data} node_field_data
LEFT JOIN {node__field_date} node__field_date ON node_field_data.nid = node__field_date.entity_id AND node__field_date.deleted = '0' AND (node__field_date.langcode = node_field_data.langcode OR node__field_date.bundle IN ( 'activite', 'agenda' ))
LEFT JOIN {paragraphs_item_field_data} paragraphs_item_field_data_node__field_date ON node__field_date.field_date_target_revision_id = paragraphs_item_field_data_node__field_date.revision_id
LEFT JOIN {node__field_filtres} node__field_filtres ON node_field_data.nid = node__field_filtres.entity_id AND node__field_filtres.deleted = '0'
LEFT JOIN {paragraphs_item_field_data} paragraphs_item_field_data_node__field_filtres ON node__field_filtres.field_filtres_target_revision_id = paragraphs_item_field_data_node__field_filtres.revision_id
LEFT JOIN {paragraph__field_date_de_debut} paragraphs_item_field_data_node__field_date__paragraph__field_date_de_debut ON paragraphs_item_field_data_node__field_date.id = paragraphs_item_field_data_node__field_date__paragraph__field_date_de_debut.entity_id AND paragraphs_item_field_data_node__field_date__paragraph__field_date_de_debut.deleted = '0'
INNER JOIN {node__field_departement} field_dep ON node_field_data.nid = field_dep.entity_id
WHERE (field_dep.field_departement_target_id = '" . get_term_departement() . "') AND ((node_field_data.status = '1') AND (node_field_data.type IN ('agenda', 'activite')))
ORDER BY paragraphs_item_field_data_node__field_date__paragraph__fiel DESC
LIMIT 11 OFFSET 0";
    $database = \Drupal::database();
    $query = $database->query($sql);
    $results = $query->fetchAll();

    return [
      "#theme" => 'recherche_activites',
      '#results' => $results
    ];

  }

}
