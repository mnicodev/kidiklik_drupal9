<?php

namespace Drupal\kidiklik_newsletter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;


/**
 * Class DefaultForm.
 */
class DefaultForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'newsletter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $node = NULL)
  {
    /* on récupére les blocs de mise en avant de type newsletter lié à la newsletter */
    //kint(current($node->get("field_entete")->getValue())["value"]);

    $mise_en_avant = \Drupal::entityTypeManager()->getStorage("node")->loadByProperties(
      ["type" => "bloc_de_mise_en_avant", 
      "field_newsletter" => $node->id(),
      "field_type" => 2,
      "field_departement" => get_term_departement(),
    ]);

    $tab_mae = [];
    $tab_mae[null] = "Choisissez un bloc ...";
    foreach ($mise_en_avant as $key => $item) {
      $tab_mae[$key] = $item->getTitle();
    }

    //kint($mise_en_avant);

    $form["h2"] = [
      "#type" => "html_tag",
      "#tag" => "H3",
      "#value" => $node->getTitle(),
      "#weight" => 0,
    ];

    $form["container"] = [
      "#prefix" => "<div class='row' id='newsletter'>",
      "#suffix" => "</div>",
    ];


    $form["container"]["group1"] = [
      "#prefix" => "<div class='col-md-8' id='content_datas'>",
      "#suffix" => "</div>",
    ];
    $form["container"]["group2"] = [
      "#prefix" => "<div class='col-md-4'>",
      "#suffix" => "</div>",
    ];
    $form["container"]["group1"]["sujet"] = [
      "#title" => "Sujet",
      "#type" => "textfield",
      "#size" => 60,
      '#required' => TRUE,
      '#value' => current($node->get("field_sujet")->getValue())["value"],
    ];

    $form["container"]["group1"]["entete"] = [
      "#title" => "Bloc d'entête",
      "#type" => "textarea",
      '#value' => current($node->get("field_entete")->getValue())["value"],

    ];

    $img_entete = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->loadByProperties(
      [
        'vid' => 'entetes_newsletter',
        'field_departement' => get_term_departement()
      ]
    );
   
    $list = [];
    $search_entete = $node->get('field_image_d_entete')->getValue();
    foreach($search_entete as $item) {
      $term = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($item['target_id']);
      if(get_term_departement() === (int)current($term->get('field_departement')->getValue())['target_id']) {
        $default_value = $term->id();
      }
    }

    $list[null] = "Choix de l'image d'entête";
    foreach($img_entete as $key => $item) {
      $list[$key] = $item->getName();
    }
    $form["container"]["group1"]["image_entete"] = [
      '#type' => 'select',
      '#title' => 'Image entête',
      '#options' => $list,
      '#default_value' => $default_value
    ];

    $form["container"]["group1"]["bloc"] = [
      "#prefix" => "<div id='blocs_datas'>",
      "#suffix" => "</div>",
    ];

    if (count($node->get('field_blocs_de_donnees'))) {
      //kint($node->get('field_blocs_de_donnees')->getValue());
      $form["container"]["group1"]["bloc_donnees"] = [
        "#title" => "contenu",
        "#type" => "hidden",
        "#attributes" => ["id" => "bloc-donnees"],
      ];
    }


    $form["container"]["group2"]["bandeau_rose"] = [
      "#type" => "checkbox",
      "#title" => "Ajouter le bandeau rose",
      "#attributes" => (current($node->get("field_bandeau_rose")->getValue())["value"] ? ["checked" => ["checked"]] : []),
      "#weight" => -10,
    ];

    $form["container"]["group2"]["mev"] = [
      "#type" => "select",

      "#title" => "Ajouter du contenu dans ma newsletter",

      "#attributes" => ["class" => ["select-bloc"]],
      "#options" => $tab_mae,

      "#ajax" => [
        "callback" => "::putBlocContent",
        "disable-refocus" => FALSE,
        "event" => "change",
        "wrapper" => "blocs_datas",
        "progress" => [
          "type" => "throbber",
          "message" => "Ajout",
        ],
      ]

    ];

    $form["container"]["nid"] = [
      "#title" => "nid",
      "#type" => "hidden",
      "#attributes" => ["id" => "nid"],
      "#value" => $node->id(),


    ];

    $form["container"]["bouton"] = [
      "#prefix" => "<div class='col-md-12'>",
      "#suffix" => "</div>",
    ];
    $form["container"]["bouton"]['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    $form["container"]["bouton"]['voir'] = [
      '#type' => 'html_tag',
      '#tag' => 'a',
      '#value' => $this->t('Voir en ligne'),
      "#attributes" => [
        "class" => "btn btn-primary",
        "target" => "blank",
        "href" => '/newsletter/' . $node->id() . $node->url(),
      ],
    ];
    $form["container"]["bouton"]['retour'] = [
      '#type' => 'html_tag',
      '#tag' => 'a',
      '#value' => $this->t('Retour à la liste'),
      "#attributes" => [
        "class" => "btn btn-warning",
        "href" => '/admin/newsletters',
      ],
      "#url" => "test",

    ];
    //$form["#redirect"]='?destination=/admin/newsletters';
    $form["#attached"]["library"][] = "kidiklik_newsletter/kidiklik_newsletter.jscss";


    return $form;
  }

  public function putBlocContent(&$form, FormStateInterface $form_state)
  {

    $fid = "";
    $url_image = "";
    $response = new AjaxResponse();
    if ($form_state->getValue("mev")) {

      /* on charge le bloc de mise en avant */
      $mise_en_avant = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
        "type" => "bloc_de_mise_en_avant", 
        "nid" => $form_state->getValue("mev")
      ]));
      $image = current($mise_en_avant->get("field_image_save")->getValue())['value'];
      if($mise_en_avant->get("field_image_save")->getValue()) {
        $url_image = 'https://www.kidiklik.fr/images/accueil/'.$image;
      } 

      $fid = current($mise_en_avant->get("field_image")->getValue())["target_id"];
      if(!empty($fid)) {
        $image = current(\Drupal::entityTypeManager()->getStorage("file")->load($fid));
        $url_image = file_create_url($image["uri"]["x-default"]); // je récupére l'url de l'image
      }
      
      $mea = [
        "titre" => $mise_en_avant->getTitle(),
        "resume" => current($mise_en_avant->get("field_resume")->getValue())["value"],
        "nid" => $mise_en_avant->id(),
        "image" => $url_image,
        "fid" => $fid,
        "lien" => $mise_en_avant->get("field_lien")->value,
      ];


      /*$renderer= \Drupal::service("renderer");
      $renderedField=$renderer->render($form["bloc"]);*/

      $response->addCommand(new InvokeCommand(NULL, 'putBlocContent', [["bloc" => $mea]]));  //
      //$response->addCommand(new ReplaceCommand('#blocs_datas', $renderedField));
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    /*foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }*/
    //kint(\Drupal::request());exit;
 
    $save_paragraph = [];
    $node = \Drupal::entityTypeManager()->getStorage("node")->load(\Drupal::routeMatch()->getParameters()->get("node")->id());

    $tb = $node->get("field_blocs_de_donnees")->getValue();
   // kint($tb);
    

    //$tb=\Drupal::request()->request->get("pid");
    foreach ($tb as $item) {
      $paragraph = \Drupal\paragraphs\Entity\Paragraph::load($item["target_id"]);
         //kint($paragraph);
      
      if (!empty($paragraph)) {
        $dept = (int)current($paragraph->get('field_departement')->getValue())['target_id'];
        //kint($dept);        kint(get_term_departement());exit;
        if((int)$dept === (int)get_term_departement()) {
          $paragraph->delete();
        } else {
          $save_paragraph[] = $paragraph;
        }
      }
    }
   // parent::validateForm($form, $form_state);return;
   
   // exit;

    if (!empty(\Drupal::request()->request->get("titre_bloc"))) {
      /*if ($node->get("field_blocs_de_donnees")) {
        $save_paragraph = ($node->get("field_blocs_de_donnees")->getValue());
      }*/
      $image_save = null;
      foreach (\Drupal::request()->request->get("titre_bloc") as $key => $item) {
        $paragraph = \Drupal\paragraphs\Entity\Paragraph::create(["type" => "bloc_donnees_titre_desc_img",]);
        $fid = \Drupal::request()->request->get("fid")[$key];
        if(!empty($fid)) {
           $image = (\Drupal::entityTypeManager()->getStorage("file")->load($fid));
        } else {
          $nid = (int)\Drupal::request()->request->get('nid_bloc')[$key];
 
          if(!empty($nid)) {
            $node_bloc =  Node::Load($nid);
            $dept = get_departement();
            $image_save = current($node_bloc->get('field_image_save')->getValue())['value'];
            $image_save = 'https://'.($dept===0?'www':$dept).'.kidiklik.fr/images/accueil/'.$image_save;
  
          }
          
         // $image_save=
        }
        $paragraph->set("field_image_save", $image_save);
        $paragraph->set("field_image", $image);
        $paragraph->set("field_titre", $item);
        $paragraph->set("field_departement", get_term_departement());
        $paragraph->set("field_resume", \Drupal::request()->request->get("resume_bloc")[$key]);
        $paragraph->set("field_lien", \Drupal::request()->request->get("lien")[$key]);
        $paragraph->set("field_nid_bloc", \Drupal::request()->request->get("nid_bloc")[$key]);
        $paragraph->save();
        $save_paragraph[] = $paragraph;
      }
      //$node->__unset("field_blocs_de_donnees");
      $node->set("field_blocs_de_donnees", $save_paragraph);
    }
    
    /* on charge le node  */
    if (\Drupal::request()->request->get("bandeau_rose")) {
        $rose = true;
    } else {
        $rose = false;
    }
    $entete_target_id = (int)\Drupal::request()->request->get('image_entete');
    if($entete_target_id !== 0) {
      $entetes = $node->get("field_image_d_entete")->getValue();
      
      $insert_entete = true;
      $save_entete = [];
      foreach($entetes as $entete) {
        $term = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load((int)$entete['target_id']);
        $dept = (int)current($term->get('field_departement')->getValue())['target_id'];
        if($dept !== get_term_departement()) {
          $save_entete[] = (int)$entete['target_id'];
        }
      }
      $node->__unset("field_image_d_entete");
      $node->save();
      $save_entete[] = $entete_target_id;
      
      foreach($save_entete as $item) {
        $node->get("field_image_d_entete")->appendItem(['target_id' => $item]);
      }
     
    }
    $node->set("field_bandeau_rose", $rose);
     
    $node->set("field_sujet", \Drupal::request()->request->get("sujet"));
    $node->set("field_entete", \Drupal::request()->request->get("entete"));//
    $node->validate();
    $node->save();

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    //kint($form);exit;
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format' ? $value['value'] : $value));
    }
  }

}
