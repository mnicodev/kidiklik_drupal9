<?php

namespace Drupal\kidiklik_admin\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;


/**
 * Provides a 'CompteurPubliciteBlock' block.
 *
 * @Block(
 *  id = "compteu_publicite_block",
 *  admin_label = @Translation("compteur Pub block"),
 * )
 */
class CompteurPubliciteBlock extends BlockBase {
	
	/**
    * {@inheritdoc}
    */
	public function build() {
		$database = \Drupal::database();
		$query=$database->select("node_field_data","n");
		$query->join("node__field_date","periode","n.nid=periode.entity_id");
		$query->join("node__field_departement","dep","dep.entity_id=n.nid");
		$query->join("node__field_format","f","f.entity_id=n.nid");
		$query->join("paragraph__field_date_de_debut","ddd","ddd.entity_id=periode.field_date_target_id");
		$query->join("paragraph__field_date_de_fin","ddf","ddf.entity_id=periode.field_date_target_id");
		$query->fields("n",["nid"]);
		$query->condition("n.type","publicite","=");
		$query->condition("dep.field_departement_target_id",get_term_departement(),"=");
		
		$pub=$query->distinct()->execute()->fetchAll();
		//ksm($pub);
		$build = [
			"#theme"=>'compteur_pub',
			"#pub" => $pub,
		];
		
		return $build;
		
	}
}
