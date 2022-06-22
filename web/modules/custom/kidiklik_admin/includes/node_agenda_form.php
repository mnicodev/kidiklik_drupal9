<?php
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
/* form agenda */
if($form_id=="node_agenda_edit_form" || $form_id=="node_agenda_form") {
   // $user=\Drupal::entityTypeManager()->getStorage("node")->load(141782);
    
  /*  $user->__set('body',htmlspecialchars_decode($user->get("body")->value));
    $user->validate();
    $user->save();*/
   
    $user = \Drupal::currentUser()->getAccount();
    $user_roles = $user->getRoles();
    $administrator = false;
    if(in_array('administrator', $user_roles)) {  
        $administrator = true;
    }
   
    $categories = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
        "vid"=>"rubriques_activite",
        "parent"=>0,
        "status"=>1
        //"field_departement"=>get_term_departement()
      ]);

      $list_cat = [];
      $list_cat['All'] = 'Choisissez une rubriques ...';
      foreach($categories as $cat) {
        $sous_categories = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
          "vid"=>"rubriques_activite",
          "parent"=>$cat->Id(),
          "status"=>1,
          "field_departement"=>get_term_departement()
        ]);
        //$output.='<div class=""><a href="/_form_search/categories/'.$cat->Id().'" >'.$cat->getName().'</a></div>';
  
        $list_cat[$cat->Id()] = $cat->getName();
        if(!empty($sous_categories)) {
          foreach($sous_categories as $sc) {
            //[$cat->getName()]
            $list_cat[$sc->Id()] = "-- ".$sc->getName();
          }
        } //else $list_cat[$cat->Id()] = $cat->getName();
       //
      }

      $form['field_rubriques']['widget']['#options'] = $list_cat;

    


    $form["type"]="agenda";
    /* on vient d'une fiche adhérent */

    if($adherent_id=\Drupal::request()->query->get("adherent")) {
        $adherent=Node::load($adherent_id);
        //ksm($form["field_adresse"]);
        $form["field_adherent"]["widget"]["#default_value"]=$adherent_id;
        $form["field_adresse"]["widget"][0]["value"]['#default_value']=$adherent->get("field_adresse")->value;
        $form["field_code_postal"]["widget"][0]["value"]['#default_value']=$adherent->get("field_code_postal")->value;
        $ville_id=current($adherent->get("field_ville")->getValue())["target_id"];
        $ville_term=\Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($ville_id);
        if(!empty($ville_term)) {
            $ville=[$ville_id=>$ville_term->getName()];
            $form["field_ville"]=[
                "widget"=>[
                    "#type"=>"select",
                    "#title"=>"Ville",
                    "#options"=>$ville,
                ],
                "#weight"=>32,
            ];
            $form["#group_children"]["field_ville"]="group_coordonnees";
        }

        $form["field_telephone"]["widget"][0]["value"]['#default_value']=$adherent->get("field_telephone")->value;
        $form["field_email"]["widget"][0]["value"]['#default_value']=$adherent->get("field_email")->value;
        $form["field_lien"]["widget"][0]["value"]['#default_value']=$adherent->get("field_lien")->value;

        $activites=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"activite","field_adherent"=>$adherent_id]);
        $activites_list=[""=>"Choisissez l'activité"];
        foreach($activites as $key=>$activite) $activites_list[$key]=$activite->getTitle();

        $form["field_activite"]=[
            "#type"=>"select",
            "#title"=>"Activités",
            "#options"=> $activites_list,
            "#weight"=>32,
        ];
        $form["#group_children"]["field_activite"]="group_coordonnees";


        $form["actions"]["retour"]=[
            "#type"=>"html_tag",
            "#tag"=>"a",
            "#value"=>"Retour",
            "#attributes"=>[
                "href"=>\Drupal::request()->query->get("destination"),//."#edit-group-agenda",
                "class"=>[
                    "btn","btn-primary"
                ]
            ],
            "#weight"=>50,
        ];

    } /* fin je viens d'une fiche adhérent */

    $form['actions']['rester'] = [
        '#type' => 'submit',
        '#value' => 'Enregistrer et rester',
        '#submit' => [
            '::submitForm',
            '::save',
            'form_redirect',
        ],
        '#access' => true,
        '#button_type' => 'primary'
    ];


    if($form_id=="node_agenda_edit_form") {
        $adherent_id=$form["field_adherent"]["widget"]["#default_value"];
        $activites=NULL;
        if(!empty($adherent_id)) {
            $activites=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"activite","field_adherent"=>$adherent_id]);
        }


        $activites_list=[""=>"Choisissez l'activité"];
        $form["field_activite"]=[
            "#type"=>"select",
            "#title"=>"Activités",

        ];
        $form["#group_children"]["field_activite"]="group_coordonnees";

        foreach($activites as $key=>$activite) $activites_list[$key]=$activite->getTitle();
        $form["field_activite"]["#options"]=$activites_list;
        if($form["field_activite_save"]["widget"][0]["value"]["#default_value"])
            $form["field_activite"]["#default_value"]=$form["field_activite_save"]["widget"][0]["value"]["#default_value"];


        $cp=$form["field_code_postal"]["widget"][0]["value"]["#default_value"];
        $query=\Drupal::entityQuery('taxonomy_term');
        $query->condition("field_code_postal",$cp);
        $villes=Term::loadMultiple($query->execute());
        $tab=array();


        foreach($villes as $ville) {
            $tab[$ville->id()]=$ville->getName();
        }
        $form["field_ville"]["#options"]=$tab;
        if($form["field_ville_save"]["widget"][0]["value"]["#default_value"])
            $form["field_ville"]["#default_value"]=$form["field_ville_save"]["widget"][0]["value"]["#default_value"];

    }

    if($administrator === true) {    
        $adherents=[]; //\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"adherent"]);
        
    } else {
        $adherents=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"adherent","field_departement"=>$term_dep]);
    }

    $tab=[];
    $tab["_none"]="Veuillez choisir un adhérent";


    foreach($adherents as $key=>$adherent) {
        $tab[$key]= $adherent->getTitle();
    }
    $form["field_adherent"]["widget"]["#options"]=$tab;

    /* le champ est configuré par défaut en multiple valeur, on bloque à une simple valeur */
    $form["field_adherent"]["widget"]["#multiple"]=FALSE;
    $form["field_adherent"]["widget"]["#ajax"]=[
        "callback"=>"getAjaxCoordonnees",
        "disable-refocus" => FALSE,
        "event" => "change",
        "wrapper" =>"coordonnees-adherent",
        "progress"=>[
            "type"=>"throbber",
            "message"=>"Analyse",
        ],

    ];

    _get_ajax_code_postal($form);
    _get_field_ville($form,9,"group_coordonnees");

    
    $form["#attached"]["library"][]="kidiklik_admin/kidiklik_admin.commands";
    if($administrator === true) {  
        $form["#attached"]["library"][]="kidiklik_admin/kidiklik_admin.commands_admin";
    }


} /* fin form agenda */