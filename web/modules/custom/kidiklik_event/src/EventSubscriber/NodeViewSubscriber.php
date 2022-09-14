<?php

namespace Drupal\kidiklik_event\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\entity\User;
use Drupal\Core\Access\AccessResult;

class NodeViewSubscriber implements EventSubscriberInterface
{

  public function checkNode(GetResponseEvent $event)
  {
    $node = \Drupal::routeMatch()->getParameters()->get("node");
    $request = $event->getRequest();
    $route_name = $request->attributes->get('_route');
    if ($route_name === 'user.register') {

    } elseif ($route_name === 'entity.node.webform.confirmation') {
      $token = \Drupal::request()->get('token');
      $email = \Drupal::request()->get('email');
      $submission = current(\Drupal::entityTypeManager()->getStorage("webform_submission")->loadByProperties([
        "token" => $token,
      ]));
      $data_submission = $submission->getData();

      if ($data_submission['newsletter'] === "1") {
        if (empty($email)) {
          $database = \Drupal::database();
          $sql = "insert into inscrits_newsletters (email, nom, prenom, dept) values ('" . $data_submission['email'] . "','" . $data_submission['nom'] . "','" . $data_submission['prenom'] . "','" . get_departement() . "')";
          $query = $database->query($sql);

          $response = new RedirectResponse(\Drupal::request()->getRequestUri() . '&email=' . $data_submission['email']);
          $response->send();
        }

      }

    } elseif (!empty($node) && $route_name == "entity.node.canonical") {
      $type = current($node->get("type")->getValue())["target_id"];

      switch ($type) {
        case "jeu_concours":

          if ($node->__isset("field_date")) {
            $date = ["debut" => strtotime($node->get('field_date_debut')->value), "fin" => strtotime($node->get('field_date_fin')->value)];
            if ($date["fin"] < strtotime(date("Y-m-d"))) {
              $event->setResponse(new RedirectResponse('/jeux-concours-termine.html'));
            }
          }
          break;
      }
    }


  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    $events[KernelEvents::REQUEST][] = array('checkNode');
    return $events;
  }

}
