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
      $submission = current(\Drupal::entityTypeManager()->getStorage("webform_submission")->loadByProperties([
        "token" => $token,
      ]));
      $data_submission = $submission->getData();
      $email = $data_submission['email'];

      if ($data_submission['newsletter'] === "1" && !empty($email)) {
          $dept = null;
          $search_dept = substr($data_submission['code_postal'], 0, 2);
          $which_dept = get_departement();
          if(!empty($search_dept) && $which_dept === 0) {
              $terms = \Drupal::entityTypeManager()
                  ->getStorage('taxonomy_term')
                  ->loadByProperties([
                      'vid' => 'departement',
                      'name' => $search_dept,
                      'status' => 1,
                  ]);

              if(!empty($terms)) {
                  $dept = $search_dept;
              }
          }
          $token = insert_registration_newsletter($data_submission['email'], $data_submission['nom'], $data_submission['prenom'],$data_submission['choisissez_newsletters'], $dept);
          $url = \Drupal::request()->getSchemeAndHttpHost();
          $response = new RedirectResponse($url . '/registration.html?token=' . $token);

          //    $response = new RedirectResponse(\Drupal::request()->getRequestUri() . '&email=' . $data_submission['email']);

           $response->send();
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
