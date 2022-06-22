<?php

namespace Drupal\kidiklik_admin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\views\Views;
use Drupal\node\Entity\Node;


/**
 * Class CompteurPubliciteController.
 *
 *
 */

class CompteurPubliciteController extends ControllerBase {

	public function content() {
		$database = \Drupal::database();

		$query=$database->select("node_field_data","n");
		//$query->join("node__field_date","periode","n.nid=periode.entity_id");
		$query->join("node__field_departement","dep","dep.entity_id=n.nid");
		$query->join("node__field_format","f","f.entity_id=n.nid");
		$query->join("node__field_date_debut","ddd","ddd.entity_id=n.nid");
		$query->join("node__field_date_fin","ddf","ddf.entity_id=n.nid");
		$query->join("taxonomy_term_field_data","tf","tf.tid=f.field_format_target_id");
		$query->fields("n",["nid","title"]);
        

		$query->fields("f",["field_format_target_id"]);
		//$query->fields("tf",["name"]);
		$query->fields("ddd",["field_date_debut_value"]);
		$query->fields("ddf",["field_date_fin_value"]);
		$query->condition("n.type","publicite","=");
		$query->condition("dep.field_departement_target_id",get_term_departement(),"=");
		
		
		if(\Drupal::request()->query->get("mois")) 
			$mois=\Drupal::request()->query->get("mois");
		else $mois=date("m");
		if(\Drupal::request()->query->get("delta")) 
			$delta=\Drupal::request()->query->get("delta");
		else $delta=0;
		
		
		$query->condition("ddd.field_date_debut_value",date("Y-m-01",mktime(0,0,0,date("m")+$delta,date("d"),date("Y"))),">=");
		$query->condition("ddd.field_date_debut_value",date("Y-m-01",mktime(0,0,0,date("m")+($delta+1),date("d"),date("Y"))),"<");

		$pub=$query->distinct()->execute()->fetchAll();
	
		if(count($pub)) {
			$cpt=1;
			$tmp=current($pub);
			// tableau des périodes pub
			$tab[str_replace("-","-",$tmp->field_date_debut_value)."_".str_replace("-","-",$tmp->field_date_fin_value)][]=[
				"format"=>$tmp->name,
				"title"=>$tmp->title,
				"nid"=>$tmp->nid,
				"date_deb"=>$tmp->field_date_debut_value,
				"date_fin"=>$tmp->field_date_fin_value,
			];

			while($next_val=next($pub)) {
				// on récupére les dates de la période convertie en valeur
				$ddd=str_replace("-","",$next_val->field_date_debut_value);
				$ddf=str_replace("-","",$next_val->field_date_fin_value);

				foreach($tab as $key=>$liste) {

					$tab_periode=explode("_",$key);
					$tab_periode[0]=str_replace("-","",$tab_periode[0]);
					$tab_periode[1]=str_replace("-","",$tab_periode[1]);
					//kint($tab_periode);
					if(($ddd==$tab_periode[0] && $ddf==$tab_periode[1]) ||
						($ddd<$tab_periode[0] && $ddf>$tab_periode[1])) {

						//$tab[$key][]=$next_val;
						$cle=$key;
					//	kint("ajout");
					}
					if(($ddd>$tab_periode[0] && $ddf<$tab_periode[1])
						|| ($ddd>$tab_periode[1]) || ($ddf<$tab_periode[0])
						||	$ddd==$tab_periode[0] && $ddf<$tab_periode[1]) {
						//$tab[$ddd."_".$ddf][]=$next_val;
						$cle=str_replace("-","-",$next_val->field_date_debut_value)."_".str_replace("-","-",$next_val->field_date_fin_value);
					//	kint("creation");
					}
				}
				$tab[$cle][]=[
					"format"=>$next_val->name,
					"title"=>$next_val->title,
					"nid"=>$next_val->nid,
					"date_deb"=>$next_val->field_date_debut_value,
					"date_fin"=>$next_val->field_date_fin_value,
				];
				//$tab[]=$tmp;

			}
			krsort($tab);
		} else $error="Il n'y a pas d'annonces publicitaire pour ce mois";
		
		$old_mois=$mois;
		$mois=date("m",mktime(0,0,0,date("m")+$delta,date("d"),date("Y")));
		
			$t=strtotime(date("Y-m")." ".$delta." MONTH");
			
		
		$liste_mois=["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"];
		
		
		$annee=date("Y",$t);
		
		return [
			
			"#theme"=>"compteur_pub",
			"#pub"=>$tab,
			"#periode"=>strtolower($liste_mois[$mois-1])." ".$annee,
			"#mois"=>$mois,
			"#delta" => (int)($delta),
			"#error" => $error,
			"#dep"=>$dep,
			"#attached" => [
				"library" => ['kidiklik_admin/kidiklik_admin.script_pub'],
			], 
			"#cache" => [
				"max-age"=>0,
			],
		];
	}
}
