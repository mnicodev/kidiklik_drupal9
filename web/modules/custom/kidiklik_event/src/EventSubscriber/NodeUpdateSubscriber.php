<?php

namespace Drupal\kidiklik_event\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\kidiklik_event\Event\NodeUpdateEvent;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class NodeUpdateSubscriber implements EventSubscriberInterface
{

  public function onNodeUpdate(NodeUpdateEvent $event)
  {
    global $_SERVER;
    $entity = $event->getEntity();
    if (!empty($_SERVER['SHELL'])) {
      return;
    }

    $type = current($entity->type->getValue())["target_id"];

    if (in_array($type, \Drupal::service("settings")->get("available_content_for_mea"))) {

      $path = \Drupal::service('path.alias_manager')->getAliasByPath($entity->url());
      $blocs = $entity->get("field_mise_en_avant")->getValue();

    }


    if ($type == "adherent") {
      $client = \Drupal::entityTypeManager()
        ->getStorage("node")
        ->load(current($entity->get("field_client")->getValue())["target_id"]);
      if (!empty($client)) {
        // on liste les adhérents du client afin de vérifier si l'adhérent est déjà enregistré
        $ok = TRUE;
        //kint($entity->id());
        foreach ($client->get("field_adherent")->getValue() as $id) {
          //	kint($id);
          if ($id["target_id"] == $entity->id()) $ok = FALSE;
        }
        //exit;
        if ($ok) {
          $client->__get("field_adherent")->appendItem($entity);
          $client->save();
        }

      }

    }

    if (in_array($type, ["activite", "agenda", "article", "reportage"], true)) {
      $adherent = \Drupal::entityTypeManager()
        ->getStorage("node")
        ->load(current($entity->get("field_adherent")->getValue())["target_id"]);
      if (!empty($adherent)) {
        $adherent->__set("field_activites", $entity);
        $adherent->save();
      }
    }

    if ($type == "agenda" || $type == "article" || $type == "reportage" || $type == "activite") {
      $image_target_id = current($entity->get('field_image')->getValue())['target_id'];
      $dates = $entity->get('field_date')->getValue();

      foreach ($entity->get('field_mise_en_avant')->getValue() as $bloc) {
        $node_bloc = Node::Load($bloc['target_id']);
	$rubriques = $entity->get('field_rubriques_activite')->getValue();
	$parent=null;
        foreach($rubriques as $rubrique) {
          $parent = current(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($rubrique['target_id']));
          break;
	}

	if(!empty($parent)) {
		$node_bloc->set('field_rubriques_activite', $parent->id());
	}
        if (!empty($image_target_id)) {
          $node_bloc->__set('field_image', ['target_id' => $image_target_id]);
        }
        
       
        $node_bloc->save();
      }
    }
  }

  public static function getSubscribedEvents()
  {
    $events[NodeUpdateEvent::NODE_UPDATE][] = ['onNodeUpdate'];
    return $events;
  }
}
