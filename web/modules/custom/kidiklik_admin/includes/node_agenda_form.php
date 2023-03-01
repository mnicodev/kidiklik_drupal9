<?php

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\views\Views;
use Drupal\views\ViewExecutable;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\kidiklik_base\KidiklikUser;
use Drupal\Core\Datetime\DrupalDateTime;

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
    $gps=[];
    foreach ($activites as $key => $activite) {
      $activites_list[$key] = $activite->getTitle();
      $gps[] = [
        'id' => $activite->id(),
        'gps' => [
          'lat' => $activite->get('field_geolocation_demo_single')->first()->get('lat')->getValue(),
          'lng' => $activite->get('field_geolocation_demo_single')->first()->get('lng')->getValue()
        ],
        'coordonnees' => [
          'lieu' => $activite->get('field_lieu')->value ?? null,
          'adresse' => $activite->get('field_adresse')->value ?? null,
          'cp' => $activite->get('field_code_postal')->value ?? null,
          'tel' => $activite->get('field_telephone')->value ?? null,
          'email' => $activite->get('field_email')->value ?? null,
        ]
      ];
    }

    $form["field_activite"] = [
      "#type" => "select",
      "#title" => "Activités",
      "#options" => $activites_list,
      "#weight" => 1,
      "#attributes" => [
        "id" => "activites",
        "data-gps" => json_encode($gps),
       ]
    ];
   /* $form["field_activite"]["widget"]["#ajax"] = [
      "callback" => "getAjaxCoordonnees",
      "disable-refocus" => FALSE,
      "event" => "change",
      "wrapper" => "coordonnees-adherent",
      "progress" => [
        "type" => "throbber",
        "message" => "Analyse",
      ],
  
    ];*/

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

function kidiklik_admin_form_node_agenda_edit_form_alter(&$form, FormStateInterface $form_state, $form_id)
{
  kidiklik_admin_form_node_agenda_form_alter($form, $form_state, $form_id);
  $cols = &$form['field_mise_en_avant']['widget']['entities']['#table_fields'];
  $cols['field_type'] = [
    'type' => 'field',
    'label' => t('Type'),
    'weight' => 1,
  ];
  $cols['field_date'] = [
    'type' => 'field',
    'label' => t('Date'),
    'weight' => 1,
  ];

  $adherent_id = $form["field_adherent"]["widget"]["#default_value"];
  $activites = NULL;
  if (!empty($adherent_id)) {
    $activites = \Drupal::entityTypeManager()->getStorage("node")->loadByProperties(["type" => "activite", "field_adherent" => $adherent_id]);
  }
  $node_agenda = \Drupal::request()->attributes->get('node');
  $activites_list = ["" => "Choisissez l'activité"];
  
  //kint($node_agenda);
  $db = \Drupal\Core\Database\Database::getConnection();
  $gps = [];
  $form["#group_children"]["field_activite"] = "group_coordonnees";
  foreach ($activites as $key => $activite) {
    $activites_list[$key] = $activite->getTitle();
    
    //$rs = $db->query('select * from villes where commune = "'.$activite->get("field_ville_save")->value.'"')->fetch();
    $gps[] = [
      'id' => $activite->id(),
      'gps' => [
        'lat' => (!empty($activite->get('field_geolocation_demo_single')->first()) ? $activite->get('field_geolocation_demo_single')->first()->get('lat')->getValue() : null),
        'lng' => (!empty($activite->get('field_geolocation_demo_single')->first()) ? $activite->get('field_geolocation_demo_single')->first()->get('lng')->getValue() : null)
      ]
    ];
  }
  $form["field_activite"] = [
    "#type" => "select",
    "#title" => "Activités",
    "#attributes" => [
      "id" => "activites",
      "data-gps" => json_encode($gps),
     ]
  ];
  $form["field_activite"]["#options"] = $activites_list;
  if (!empty($form["field_activite_save"]["widget"][0]["value"]["#default_value"])) {
    $form["field_activite"]["#default_value"] = $form["field_activite_save"]["widget"][0]["value"]["#default_value"];
  } else if (!empty(current($node_agenda->get('field_activite')->getValue())['target_id'])) {
    $form["field_activite"]["#default_value"] = current($node_agenda->get('field_activite')->getValue())['target_id'];
  }
  $cp = $form["field_code_postal"]["widget"][0]["value"]["#default_value"];

}