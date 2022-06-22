<?php
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
/* on reformate le choix des activités pour ne choisir que des enfants */
if($form_id=="node_activite_edit_form" || $form_id=="node_activite_form") {


    $form["#attached"]["library"][]="kidiklik_admin/kidiklik_admin.commands";
    $form["type"]="activite";
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


    unset($form["field_date"]["widget"]['add_more']);
    //$form["field_departement"]["widget"][0]['target_id']['#default_value']=get_departement();
//ksm($form["field_departement"]["widget"][0]['target_id']['#default_value']);

    if($adherent_id=\Drupal::request()->query->get("adherent") && $form_id=="node_activite_form")  {
        $adherent=Node::load($adherent_id);
        //ksm($form["field_adresse"]);
        $form["field_adherent"]["widget"]["#default_value"]=$adherent_id;
        $form["field_adresse"]["widget"][0]["value"]['#default_value']=$adherent->get("field_adresse")->value;
        $form["field_code_postal"]["widget"][0]["value"]['#default_value']=$adherent->get("field_code_postal")->value;
        $ville_id=current($adherent->get("field_ville")->getValue())["target_id"];
        $ville_term=\Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($ville_id);
        $ville=[$ville_id=>$ville_term->getName()];
        $form["field_ville"]["#options"]=$ville;

        $form["#group_children"]["field_ville"]="group_coordonnees";
        $form["field_telephone"]["widget"][0]["value"]['#default_value']=$adherent->get("field_telephone")->value;
        $form["field_email"]["widget"][0]["value"]['#default_value']=$adherent->get("field_email")->value;
        $form["field_lien"]["widget"][0]["value"]['#default_value']=$adherent->get("field_lien")->value;
        //ksm($form);

        $form["actions"]["retour"]=[
            "#type"=>"html_tag",
            "#tag"=>"a",
            "#value"=>"Retour",
            "#attributes"=>[
                "href"=>\Drupal::request()->query->get("destination"),//."#edit-group-activite",
                "class"=>[
                    "btn","btn-primary"
                ]
            ],
            "#weight"=>50,
        ];

    } else {/* formattage de la balise select avec affichage par catégorie */
        if(get_departement() !== 0) {
            $rub=$form["field_rubriques_activite"]["widget"]["#options"];
            $tab=[];
            $n="";
            foreach($rub as $k=>$r) {
                if($k!="_none") {
                    $t=taxonomy_term_load($k);
                    $p=current($t->parent->getValue()[0]);
                    if(!(int)$p) {
                        $n=current($t->name->getValue()[0]);
                    } else {
                        if(current($t->get("field_departement")->getValue())["target_id"]==$term_dep)
                            $tab[$n][$k]=current($t->name->getValue()[0]);
                    }
                }
            }
            $form["field_rubriques_activite"]["widget"]["#options"]=$tab;

            $form["field_rubriques_activite"]["widget"]["#size"]=1;

        }
        $form["#redirect"]="/admin/activites";

        $adherents=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"adherent","field_departement"=>$term_dep]);
        $tab=[];
        $tab["_none"]="Veuillez choisir un adhérent";
        foreach($adherents as $key=>$adherent) {
            $tab[$key]=$adherent->getTitle();
        }
        $form["field_adherent"]["widget"]["#options"]=$tab;

        if($adherent_id=\Drupal::request()->query->get("adherent")) {
            $form["actions"]["retour"]=[
                "#type"=>"html_tag",
                "#tag"=>"a",
                "#value"=>"Retour",
                "#attributes"=>[
                    "href"=>\Drupal::request()->query->get("destination"),//."#edit-group-activite",
                    "class"=>[
                        "btn","btn-primary"
                    ]
                ],
                "#weight"=>50,
            ];
        }

    }
    //ksm($form);
    _get_ajax_code_postal($form);
    _get_field_ville($form,48,"group_coordonnees");

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

} /* fin form activite */