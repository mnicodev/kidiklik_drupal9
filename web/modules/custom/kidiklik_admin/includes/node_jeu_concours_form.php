<?php
use Drupal\user\Entity\User;

if($form_id=="node_jeu_concours_form" || $form_id=="node_jeu_concours_edit_form") {
		unset($form["field_date"]["widget"]["add_more"]);
		$node=\Drupal::routeMatch()->getParameters()->get("node");
		$user = User::Load(\Drupal::currentUser()->id());
		if (!$user->hasRole('administrator') && !$user->hasRole('administrateur_de_departement')) {
			unset($form["#group_children"]["group_partage"]);
			unset($form['field_tous_les_sites']);
			unset($form["field_partage_departements"]);
		  } else {
			  foreach ($form['field_partage_departements']['widget']['#options'] as $key => $item) {

				$nom_departement = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($key);
				$form['field_partage_departements']['widget']['#options'][$key] = $nom_departement->get("field_nom")->value . " (" . $nom_departement->getName() . ")" . ($nom_departement->get("field_region")->value ? " - <i>" . $nom_departement->get("field_region")->value . "</i>" : "");
	  
		 	 }
		  }
		  $form["#attached"]["library"][]="kidiklik_admin/kidiklik_admin.commands";
		
		if(!empty($node)) {
			$form["actions"]["voir"]=[
			  "#type"=>"html_tag",
			  "#tag"=>"a",
			  "#value"=>"Voir",
			  "#attributes"=>["class"=>"btn btn-primary","target"=>"blank","href"=>""/*$node->url()*/],
			];
			$date = null;
			if($node->__isset("field_date")) {
				$date=["debut"=>strtotime($node->get('field_date_debut')->value),"fin"=>strtotime($node->get('field_date_fin')->value)];
			}

			/* la date est dépassé, le jeu est terminé, on peut afficher le message ainsi que le bouton */
			if($date["fin"]<strtotime(date("Y-m-d"))) {

				/* le champs devant contenir les gagnants n'est pas encore renseigné */
				if($form["field_gagnants_selectionnes"]["widget"][0]["value"]["#default_value"]==NULL) {
					$form["info"]=[
						"#type"=>"html_tag",
						"#tag"=>"div",
						"#value"=>"Le jeu concours est terminé. Vous pouvez sélectionner les gagnants !",
						"#weight"=>80,
						"#attributes"=>["class"=>"alert alert-warning","role"=>"alert"]
					];

					$form["actions"]["selection_gagnants"]=[
					  "#type"=>"html_tag",
					  "#tag"=>"a",
					  "#value"=>"Sélectionner les gagnants",
					  "#attributes"=>["class"=>"btn btn-warning","target"=>"blank","href"=>"/admin/jeux-concours/getGagnants/".$node->id()],
					  "#weight"=>100,

					];


				} else {
					/* l'admin a cliqué sur le bouton de sélection des gagnants, cela a renseigné le champs*/
					$form["csv_gagnants_selectionnes"]=[
							"#type"=>"html_tag",
							"#tag"=>"div",
							"#prefix"=>"<div><label>Gagnants sélectionnés</label>",
							"#suffix"=>"</div>",
							"#value"=>str_replace("\n","<br>",$form["field_gagnants_selectionnes"]["widget"][0]["value"]["#default_value"]),
							"#attributes"=>[
								"class"=>"alert alert-success"
							]
						];
            $form["actions"]["selection_gagnants"]=[
              "#type"=>"html_tag",
              "#tag"=>"a",
              "#value"=>"Exporter les gagnants",
              "#attributes"=>["class"=>"btn btn-info","target"=>"blank","href"=>"/admin/jeux-concours/getGagnants/".$node->id()],
              "#weight"=>100,

            ];

				}

				unset($form["field_gagnants_selectionnes"]);



			}
		}


		  $adherents=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"adherent","field_departement"=>$term_dep]);
			$tab=[];
			$tab["_none"]="Veuillez choisir un client";
			foreach($adherents as $key=>$adherent) {
				$tab[$key]=$adherent->getTitle();
			}
			$form["field_adherent"]["widget"]["#multiple"]=FALSE;
			$form["field_adherent"]["widget"]["#options"]=$tab;
	} /* fin jeu concours */
