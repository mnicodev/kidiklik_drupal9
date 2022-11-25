<?php

namespace Drupal\kidiklik_event\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\kidiklik_event\Event\NodeInsertEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\HttpResponse;
use Drupal\kidiklik_base\KidiklikEntity;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class NodeInsertSubscriber implements EventSubscriberInterface
{
  public function onNodeInsert(NodeInsertEvent $event)
  {
    global $_POST, $_SERVER;
    if (!empty($_SERVER['SHELL'])) {
      return;
    }
    /* on récupére le département de la config */
    $dep = \Drupal::service("settings")->get("dep");

    $entity = $event->getEntity();
    /* on récupére le type du contenu */
    $type = current($entity->type->getValue())["target_id"];
    /* on recherche le département dans le vocabulaire de taxonomie des département */
    $term = current(\Drupal::entityTypeManager()
      ->getStorage("taxonomy_term")
      ->loadByProperties(['name' => $dep]));
    /* il existe */
    if ($term !== FALSE) {
      $term_id = current($term->tid->getValue());
      /* le type d'entité correspond elle aux entités ayant un champ de département */
      if (in_array($type, \Drupal::service("settings")->get("available_content"))) {
        /* on affecte le département */
        $entity->__set('field_departement', $term->id());
        $entity->save();
      }

    }

    if ($type == "message_contact") {
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'kidiklik_event';
      $key = 'create_message';
      $params = [];
      $to = $term->get('field_e_mail')->value;
      $params['from'] = 'noreply@kidiklik.fr';
      $params['body'] = $entity->get("field_votre_question")->value;
      $params['subject'] = sprintf('Message de %s %s',$entity->get('field_nom')->value,$entity->get('field_prenom')->value);
      $params['message'] = $entity->get("field_votre_question")->value;
      $params['title'] = sprintf('Message de %s %s',$entity->get('field_nom')->value,$entity->get('field_prenom')->value);
      $langcode = "fr";
      $send = true;
      //kint($entity->get("field_votre_question")->value);exit;
      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
      if ($result['result'] !== true) drupal_set_message("Un probléme d'envoi est survenu.", "error");
      else  drupal_set_message("Votre message a bien été envoyé");
    }

    if ($type == "publicite" || $type == "activite" || $type == "agenda" || $type == "article" || $type == "reportage") {

      $adherent = \Drupal::entityTypeManager()
        ->getStorage("node")
        ->load(current($entity->get("field_adherent")->getValue())["target_id"]);
      if (!empty($adherent)) {
        $adherent->__set("field_activites", $entity);
        $adherent->save();
      }
    }
    if ($type == "activite" || $type == "agenda" || $type == "article" || $type == "reportage") {

      $blocs = $entity->field_mise_en_avant->getValue();
      if (!empty($blocs)) {
        foreach ($blocs as $bloc) {
          $bloc = Node::load($bloc['target_id']);
          if (empty($bloc->get('field_lien')->value)) {
            $bloc->set('field_lien', \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $entity->id()));
            $bloc->save();
          }
        }
      }
      $image_target_id = current($entity->get('field_image')->getValue())['target_id'];
      $dates = $entity->get('field_date')->getValue();

      foreach ($entity->get('field_mise_en_avant')->getValue() as $bloc) {
        $n = Node::Load($bloc['target_id']);
        if (!empty($image_target_id)) {
          $n->__set('field_image', ['target_id' => $image_target_id]);
        }
        if (!empty($dates)) {
          $n->__unset("field_date");
          $n->save();
          foreach ($dates as $date) {
            $d = Paragraph::Load($date['target_id']);
            if ($type == "agenda") {
              $dda = date('Y-m-d', strtotime($d->get('field_date_de_debut')->value . ' - 5 days'));
              $dfa = $d->get('field_date_de_fin')->value; //date('Y-m-d', strtotime($d->get('field_date_de_fin')->value . ' - 7 days'));
            } else {
              $dda = $d->get('field_date_de_debut')->value;
              $dfa = $d->get('field_date_de_fin')->value;
            }
            $nd = Paragraph::create([
              'type' => 'date',
              'field_date_de_debut' => [
                'value' => $dda
              ],
              'field_date_de_fin' => [
                'value' => $dfa
              ],
            ]);

            $n->get("field_date")->appendItem($nd);

          }
        }
        $n->save();
      }
    }

    if ($type == "bloc_de_mise_en_avant") {

      $adherent = \Drupal::entityTypeManager()
        ->getStorage("node")
        ->load(current($entity->get("field_adherent_cache")->getValue())["value"]);
      //kint($entity->get('field_image_entite')->value);exit;
      if (!empty($entity->get('field_image_entite')->value)) {
        $entity->__set('field_image', ['target_id' => $entity->get('field_image_entite')->value]);
        $entity->__unset('field_image_entite');
        $entity->save();
      }
      $image_entity = $entity->get('field_image_entite')->value;
      if (!empty($adherent)) {
        $adherent->__set("field_activites", $entity);
        $adherent->save();

        $entity->__set("field_adherent", $adherent);
        $entity->save();
      }

      /**
       *  On ne prend plus en compte le champ bloc mise en avant
       * les bloc de newsletter seront indépendants et marqué par le champs newsletter du bloc
       */

    }

  }

  public static function getSubscribedEvents()
  {
    $events[NodeInsertEvent::NODE_INSERT][] = ['onNodeInsert'];
    return $events;
  }
}
