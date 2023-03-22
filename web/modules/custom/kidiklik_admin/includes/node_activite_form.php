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

function kidiklik_admin_form_node_activite_form_alter(&$form, FormStateInterface $form_state, $form_id)
{
  $service = \Drupal::service('kidiklik.service');

  $term_dep = $service->getTermDepartement();
  $departement = $service->getDepartement();
  
  $form["#attached"]["library"][] = "kidiklik_admin/kidiklik_admin.commands";
  $form["type"] = "activite";
  /* le champ est configuré par défaut en multiple valeur, on bloque à une simple valeur */
  $liste_adherents['_none'] = '';
  foreach ($form["field_adherent"]["widget"]["#options"] as $key => $item) {
    $liste_adherents[$key] = $item;
  }
  $form["field_adherent"]["widget"]["#options"] = $liste_adherents;
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
  if ($departement !== 0) {
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

  //unset($form["field_date"]["widget"]['add_more']);

  $adherent_id = null;
  $adherent = null;
  if (!empty(\Drupal::request()->query->get("adherent_id"))) {
    $adherent_id = \Drupal::request()->query->get("adherent_id");
  }
  if (!empty(\Drupal::request()->query->get("ref_id"))) {
    $adherent_id = \Drupal::request()->query->get("ref_id");
  }
  if ($adherent_id !== null && $form_id == "node_activite_form") {
    $adherent = Node::load($adherent_id);

    $form["field_adherent"]["widget"]["#default_value"] = $adherent_id;
    $form["field_adresse"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_adresse")->value;
    $form["field_code_postal"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_code_postal")->value;
    $form["#group_children"]["field_ville"] = "group_coordonnees";
    $form["field_telephone"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_telephone")->value;
    $form["field_email"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_email")->value;
    $form["field_lien"]["widget"][0]["value"]['#default_value'] = $adherent->get("field_lien")->value;

    $form["actions"]["retour"] = [
      "#type" => "html_tag",
      "#tag" => "a",
      "#value" => "Retour",
      "#attributes" => [
        "href" => \Drupal::request()->query->get("destination"),//."#edit-group-activite",
        "class" => [
          "btn", "btn-primary"
        ]
      ],
      "#weight" => 50,
    ];

  } else {/* formattage de la balise select avec affichage par catégorie */

    $form["#redirect"] = "/admin/activite";
    if ($adherent_id = \Drupal::request()->query->get("adherent")) {
      $form["actions"]["retour"] = [
        "#type" => "html_tag",
        "#tag" => "a",
        "#value" => "Retour",
        "#attributes" => [
          "href" => \Drupal::request()->query->get("destination"),//."#edit-group-activite",
          "class" => [
            "btn", "btn-primary"
          ]
        ],
        "#weight" => 50,
      ];
    }

  }
  //ksm($form);
  _get_ajax_code_postal($form);
  _get_field_ville($form, 47, "group_coordonnees", !empty($adherent) ? $adherent->get("field_ville_save")->value : null);

  add_record_and_stay_button($form);
}

function kidiklik_admin_form_node_activite_edit_form_alter(&$form, FormStateInterface $form_state, $form_id)
{
 
  kidiklik_admin_form_node_activite_form_alter($form, $form_state, $form_id);
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
}