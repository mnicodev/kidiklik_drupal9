<?php

namespace Drupal\kidiklik_front\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a 'SortiesBoutonBlock' block.
 *
 * @Block(
 *  id = "sorties_bouton_block",
 *  admin_label = @Translation("Sorties bouton block"),
 * )
 */
class SortiesBoutonBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $node = \Drupal::request()->get('node');

    $build = [
	"#theme" => 'sortie_moment_bouton',
      "#cache" => [
        "max-age" => 0,
        "contexts" => [],
        "tags" => [],
      ],
    ];

   
    /* patch avant de voir ce qui pose probleme */
    if (!empty($node)) {
	    $database = \Drupal::database();
	    $sql_activites = "SELECT node_field_data.nid AS nid FROM {node_field_data} node_field_data
		LEFT JOIN {node__field_date} node__field_date ON node_field_data.nid = node__field_date.entity_id AND node__field_date.deleted = '0' AND (node__field_date.langcode = node_field_data.langcode OR node__field_date.bundle IN ( 'activite', 'agenda' ))
		LEFT JOIN {paragraphs_item_field_data} paragraphs_item_field_data_node__field_date ON node__field_date.field_date_target_revision_id = paragraphs_item_field_data_node__field_date.revision_id
		LEFT JOIN {paragraph__field_date_de_fin} paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin ON paragraphs_item_field_data_node__field_date.id = paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.entity_id AND paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.deleted = '0'
		LEFT JOIN {node__field_activite} node__field_activite ON node_field_data.nid = node__field_activite.entity_id AND node__field_activite.deleted = '0'
		WHERE ((node__field_activite.field_activite_target_id = :target_id)) AND ((node_field_data.status = '1') AND (node_field_data.type IN ('agenda')) AND ((DATE_FORMAT(paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.field_date_de_fin_value, '%Y-%m-%d') >= DATE_FORMAT(:date, '%Y-%m-%d'))))";
	    $sql_agendas = "SELECT node_field_data.nid AS nid FROM {node_field_data} node_field_data
		LEFT JOIN {node__field_date} node__field_date ON node_field_data.nid = node__field_date.entity_id AND node__field_date.deleted = '0' AND (node__field_date.langcode = node_field_data.langcode OR node__field_date.bundle IN ( 'activite', 'agenda' ))
		LEFT JOIN {paragraphs_item_field_data} paragraphs_item_field_data_node__field_date ON node__field_date.field_date_target_revision_id = paragraphs_item_field_data_node__field_date.revision_id
		LEFT JOIN {paragraph__field_date_de_fin} paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin ON paragraphs_item_field_data_node__field_date.id = paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.entity_id AND paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.deleted = '0'
		LEFT JOIN {node__field_adherent} node__field_adherent ON node_field_data.nid = node__field_adherent.entity_id AND node__field_adherent.deleted = '0' AND (node__field_adherent.langcode = node_field_data.langcode OR node__field_adherent.bundle = 'agenda')
		WHERE ((node__field_adherent.field_adherent_target_id = :target_id)) AND ((node_field_data.status = '1') AND (node_field_data.type IN ('agenda')) AND ((DATE_FORMAT(paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.field_date_de_fin_value, '%Y-%m-%d') >= DATE_FORMAT(:date, '%Y-%m-%d'))))";

      $results = [];
          if ($node->getType() === 'activite') {
            $build['#ref_act'] = $node->id();
            $results = $database->query($sql_activites, [
              ':date' => date('y-m-d'),
              ':target_id' => $node->id()
            ])->fetchAll();
          }

      if(count($results) === 0 || $node->getType() === 'agenda') {
        unset($build['#ref_act']);
        $results = $database->query($sql_agendas, [
          ':date' => date('y-m-d'),
          ':target_id' => current($node->get('field_adherent')->getValue())['target_id']
        ])->fetchAll();
        if(count($results) > 0) {
          $build['#ref_adh'] = current($node->get('field_adherent')->getValue())['target_id'];
        }
      }
    }
    return $build;
  }

}
