<?php
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;

function kidiklik_admin_form_node_agenda_form_alter(&$form, FormStateInterface $form_state, $form_id)
{

  $term_dep = get_term_departement();
  $user = \Drupal::currentUser()->getAccount();
  $form['#validate'][] = 'kidiklik_admin_form_bloc_validate';
  $liste_adherents[''] = '';
  foreach ($form["field_adherent"]["widget"]["#options"] as $key => $item) {
    $liste_adherents[$key] = $item;
  }

  $user_roles = $user->getRoles();
  $administrator = false;
  if (in_array('administrator', $user_roles)) {
    $administrator = true;
  }
  $n = \Drupal::request()->attributes->get('node');


  $categories = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties([
    "vid" => "rubriques_activite",
    "parent" => 0,
    "status" => 1
    //"field_departement"=>get_term_departement()
  ]);


  if (get_departement() !== 0) {
    $rub = $form["field_rubriques_activite"]["widget"]["#options"];
    $tab = [];
    $n = "";
    foreach ($rub as $k => $r) {
      if ($k != "_none") {
        $t = taxonomy_term_load($k);
        $p = current($t->parent->getValue()[0]);
        if (!(int)$p) {
          $n = current($t->name->getValue()[0]);
        } else {
          if (current($t->get("field_departement")->getValue())["target_id"] == $term_dep)
            $tab[$n][$k] = current($t->name->getValue()[0]);
        }
      }
    }
    $form["field_rubriques_activite"]["widget"]["#options"] = $tab;

    $form["field_rubriques_activite"]["widget"]["#size"] = 1;

  }


  $form["type"] = "agenda";
  /* on vient d'une fiche adhérent */
  $adherent_id = null;
  $adherent = null;
  if (!empty(\Drupal::request()->query->get("adherent_id"))) {
    $adherent_id = \Drupal::request()->query->get("adherent_id");
  }
  if (!empty(\Drupal::request()->query->get("ref_id"))) {
    $adherent_id = \Drupal::request()->query->get("ref_id");
  }

  if ($adherent_id !== null && $form_id == "node_agenda_form") {
    $adherent = Node::load($adherent_id);

    $form["field_adherent"]["widget"]["#default_value"] = $adherent_id;
    $form["field_adresse"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_adresse")->value;
    $form["field_ville_save"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_ville_save")->value;
    $form["field_code_postal"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_code_postal")->value;
    $form["field_code_postal"]['#weight'] = 5;


    $form["field_telephone"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_telephone")->value;
    $form["field_email"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_email")->value;
    $form["field_lien"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_lien")->value;

    $activites = \Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type" => "activite", "field_adherent" => $adherent_id]);
    $activites_list = ["" => "Choisissez l'activité"];
    foreach ($activites as $key => $activite) $activites_list[$key] = $activite->getTitle();

    $form["field_activite"] = [
      "#type" => "select",
      "#title" => "Activités",
      "#options" => $activites_list,
      "#weight" => 1,
    ];
    $form["#group_children"]["field_activite"] = "group_coordonnees";


    $form["actions"]["retour"] = [
      "#type" => "html_tag",
      "#tag" => "a",
      "#value" => "Retour",
      "#attributes" => [
        "href" => \Drupal::request()->query->get("destination"),//."#edit-group-agenda",
        "class" => [
          "btn", "btn-primary"
        ]
      ],
      "#weight" => 50,
    ];

  } /* fin je viens d'une fiche adhérent */
  add_record_and_stay_button($form);


  if ($administrator === true) {
    $adherents = \Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type" => "adherent"]);
    $tab = [];
    $tab["_none"] = "Veuillez choisir un adhérent";


    foreach ($adherents as $key => $adherent) {
      $tab[$key] = $adherent->getTitle();
    }
    $form["field_adherent"]["widget"]["#options"] = $tab;
  }

  /* le champ est configuré par défaut en multiple valeur, on bloque à une simple valeur */
  $form["field_adherent"]["widget"]["#multiple"] = FALSE;
  $form["field_adherent"]["widget"]["#ajax"] = [
    "callback" => "getAjaxCoordonnees",
    "disable-refocus" => FALSE,
    "event" => "change",
    "wrapper" => "coordonnees-adherent",
    "progress" => [
      "type" => "throbber",
      "message" => "Analyse",
    ],

  ];

  _get_ajax_code_postal($form);
  _get_field_ville($form, 16, "group_coordonnees", !empty($adherent) ? $adherent->get("field_ville_save")->value : null);


  $form["#attached"]["library"][] = "kidiklik_admin/kidiklik_admin.commands";
  if ($administrator === true) {
    $form["#attached"]["library"][] = "kidiklik_admin/kidiklik_admin.commands_admin";
  }
}