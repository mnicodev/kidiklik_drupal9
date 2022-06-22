<?php

namespace Drupal\kidiklik_front\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;


/**
 * Provides a 'PubBlock' block.
 *
 * @Block(
 *  id = "pub_block",
 *  admin_label = @Translation("Pub block"),
 * )
 */
class PubBlock extends BlockBase {
    public function blockForm($form, FormStateInterface $form_state) {
        //$form = parent::blockForm($form, $form_state);
        ksm($this->configuration['format_pub']);
        $form["format"]=[
            "#type"=>"select",
            "#title" => "Format",
            "#options"=>["carre"=>"carre","large"=>"large","haut"=>"haut"],
        ];
        
        return $form;
       
    }
    
     /**
       * {@inheritdoc}
       */
      public function blockSubmit($form, FormStateInterface $form_state) {
        parent::blockSubmit($form, $form_state);
        $values = $form_state->getValues();
        $this->configuration['format_pub'] = $values['format'];
      }
      
    /**
    * {@inheritdoc}
    */
  public function build() {
    $term_format=current(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(["vid"=>"format_publicite","field_style_image"=>$this->configuration['format_pub']]));

    
    $query="SELECT node_field_data.nid AS nid, paragraphs_item_field_data_node__field_date.id AS paragraphs_item_field_data_node__field_date_id, RAND() AS random_field
FROM node_field_data
LEFT JOIN node__field_date ON node_field_data.nid = node__field_date.entity_id 
INNER JOIN paragraphs_item_field_data paragraphs_item_field_data_node__field_date ON node__field_date.field_date_target_revision_id = paragraphs_item_field_data_node__field_date.revision_id
INNER JOIN node__field_format node__field_format ON node_field_data.nid = node__field_format.entity_id AND node__field_format.deleted = '0'
LEFT JOIN node__field_departement node__field_departement ON node_field_data.nid = node__field_departement.entity_id AND node__field_departement.deleted = '0' 
LEFT JOIN node__field_tous_les_sites node__field_tous_les_sites ON node_field_data.nid = node__field_tous_les_sites.entity_id AND node__field_tous_les_sites.deleted = '0'
LEFT JOIN node__field_partage_departements node__field_partage_departements ON node_field_data.nid = node__field_partage_departements.entity_id AND node__field_partage_departements.deleted = '0'
LEFT JOIN paragraph__field_date_de_debut paragraphs_item_field_data_node__field_date__paragraph__field_date_de_debut ON paragraphs_item_field_data_node__field_date.id = paragraphs_item_field_data_node__field_date__paragraph__field_date_de_debut.entity_id AND paragraphs_item_field_data_node__field_date__paragraph__field_date_de_debut.deleted = '0'
LEFT JOIN paragraph__field_date_de_fin paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin ON paragraphs_item_field_data_node__field_date.id = paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.entity_id AND paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.deleted = '0'
LEFT JOIN node__field_nombre_affichage_possible node__field_nombre_affichage_possible ON node_field_data.nid = node__field_nombre_affichage_possible.entity_id
left JOIN node_counter nc ON node_field_data.nid = nc.nid
left JOIN node__field_conditionne_nombre_aff cond_nb_aff ON node_field_data.nid = cond_nb_aff.entity_id
left JOIN node__field_nombre_affichage_possible nb_aff_poss ON node_field_data.nid = nb_aff_poss.entity_id";

    $where=" WHERE (
    (node__field_format.field_format_target_id = '".$term_format->id()."')) AND (
    (
        (node_field_data.status = '1') AND 
        (node_field_data.type IN ('publicite'))
    )
    AND (
        (node__field_departement.field_departement_target_id = '".get_term_departement()."') OR 
        (node__field_tous_les_sites.field_tous_les_sites_value = '1') OR 
        (node__field_partage_departements.field_partage_departements_target_id = '".get_term_departement()."')
    ) AND (
        (
        (
            DATE_FORMAT(paragraphs_item_field_data_node__field_date__paragraph__field_date_de_debut.field_date_de_debut_value, '%Y-%m-%d') <= DATE_FORMAT('".date("Y-m-d")."', '%Y-%m-%d')
        ) AND (
            DATE_FORMAT(paragraphs_item_field_data_node__field_date__paragraph__field_date_de_fin.field_date_de_fin_value, '%Y-%m-%d') >= DATE_FORMAT('".date("Y-m-d")."', '%Y-%m-%d')
        )
    ) or (
        (cond_nb_aff.field_conditionne_nombre_aff_value = '1') AND ((nc.totalcount<=nb_aff_poss.field_nombre_affichage_possible_value))
    )
        )
)
ORDER BY random_field ASC
LIMIT 1 OFFSET 0";
  

    $db=\Drupal\Core\Database\Database::getConnection();
    $rs=$db->query($query.$where)->fetchAll();
    $result=[];
    
    $style=ImageStyle::load("pub_".$this->configuration['format_pub']);
    
    foreach($rs as $item) {
        $node=Node::load($item->nid);
        $fid=current($node->get("field_image")->getValue())["target_id"];
        $img=\Drupal::entityManager()->getStorage('file')->load($fid);
        if($node->get("field_script")->value) $result["script"]=$node->get("field_script")->value;
        else $result["img"]=$style->buildUrl($img->uri->value);
        $result["url"]=current($node->get("field_url")->getValue())["uri"];
        $result["nid"]=$node->id();
    }
    $result["class"]=$this->configuration['format_pub'];

	
	$path_stat=\Drupal::request()->getBasePath()."/".drupal_get_path("module","statistics")."/statistics.php";
	
	
    $build = [
		"#theme"=>'publicite_block',
		"#content" => $result,
		"#path_stat"=>$path_stat,
		/*"#attached" => [
		    "library" => ["kidiklik_front/kidiklik_front_publicite/kidiklik_front_publicite.actions"],
		],*/
		"#cache" => [
			"max-age"=>0,
			"contexts"=>[],
			"tags"=>[],
		],
		//"#markup" 
    ];

    return $build;
  }
}
