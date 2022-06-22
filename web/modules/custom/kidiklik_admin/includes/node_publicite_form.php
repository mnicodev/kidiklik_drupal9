<?php
if($form_id=="node_publicite_form" || $form_id=="node_publicite_edit_form") {

    unset($form["field_date"]["widget"]["add_more"]);
    $form["#redirect"]="/admin/publicites";
    $form["field_adherent"]["widget"]["#multiple"]=FALSE;
    //$adherents=\Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type"=>"adherent","field_departement"=>$term_dep]);
    $tab=[];
    $tab["_none"]="Veuillez choisir un adhérent";
    /*foreach($adherents as $key=>$adherent) {
        $tab[$key]=$adherent->getTitle();
    }*/
    //$form["field_adherent"]["widget"]["#options"]=$tab;
//ksm($form);
    if(!in_array("administrator",\Drupal::currentUser()->getRoles())) {
        unset($form["#group_children"]["group_partage"]);
        unset($form['field_tous_les_sites']);
        unset($form['field_national']);
        unset($form["field_partage_departements"]);
        //$form['field_partage_departements']["widget"]["#type"]="radios";
        /*$form['field_partage_departements']["widget"]["#multiple"]=false;
        $form["admin"]=[
            "#type"=>"html_tag",
            "#tag"=>"input",
            "#attributes"=>[
                "id"=>"gestion_dep",
                "type"=>"hidden"
            ],
        ];
        array_pop($form["field_format"]["widget"]["#options"]);*/

    } else {
        unset($form['field_national']);
        $form["#submit"][]="kidiklik_admin_form_submit";
        foreach($form['field_partage_departements']['widget']['#options'] as $key=>$item) {
            //if(!(int)$item->__toString()) unset($form['field_partage_departements']['widget']['#options'][$key]);
            //else {
            //
                $nom_departement=\Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($key);
                $form['field_partage_departements']['widget']['#options'][$key]=$nom_departement->get("field_nom")->value." (".$nom_departement->getName().")".($nom_departement->get("field_region")->value?" - <i>".$nom_departement->get("field_region")->value."</i>":"");
            //}
        }
    }

    $form["#attached"]["library"][]="kidiklik_admin/kidiklik_admin.commands";
    $form["#validate"][]="kidiklik_admin_form_publicite_validate";
    $form["#submit"][]="kidiklik_admin_form_publicite_submit";


} /* fin pub */