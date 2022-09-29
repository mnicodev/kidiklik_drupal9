<?php


namespace Drupal\kidiklik_event\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\HttpResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InitSubscriber implements EventSubscriberInterface
{

  public function checkRedirect(GetResponseEvent $event)
  {
	  $request = $event->getRequest();

    $node = \Drupal::routeMatch()->getParameters()->get("node");
    if (!empty($node)) {
      if (in_array($node->getType(), ['client', 'adherent']) && strstr($request->getPathInfo(), 'edit') === false) {
        $redirect = new RedirectResponse('/');
        $redirect->send();
      }
    }

    preg_match("/admin/", $request->getRequestUri(), $rs);
    if (count($rs) > 0 && !in_array('administrator', \Drupal::currentUser()->getAccount()->getRoles())) {
      $term_dep = (int)current(user_load(\Drupal::currentUser()->getAccount()->id())->get('field_departement')->getValue())['target_id'];
      $user_roles = \Drupal::currentUser()->getAccount()->getRoles();
      //kint(\Drupal::currentUser()->getAccount()->getRoles());exit;
      if ($term_dep !== (int)get_term_departement()) {
        drupal_set_message(t("Vous n'êtes pas autorisé à accéder à ce gestionnaire"), 'error');
        $redirect = new RedirectResponse('/');
        $redirect->send();
        exit;
      }
    }
    $url = str_replace('/', '', $request->getRequestUri());
    if ($url === 'admin') {
      if (in_array('administrateur_de_departement', $user_roles)) {
        $redirect = new RedirectResponse('/admin/dashboard');
        $redirect->send();
        exit;
      }
    }

    //kint($t);
    $dep_status = current(current(\Drupal::entityTypeManager()
      ->getStorage("taxonomy_term")
      ->loadByProperties([
        'name' => get_departement(),
        'vid' => 'departement'
      ]))
      ->get("status")
      ->getValue());

    if (!(int)$dep_status["value"] && get_departement() !== 0) {

      $tab = explode(".", \Drupal::request()->getHost());
      array_shift($tab);
      $url = "http://www." . implode(".", $tab) . "/";

      $redirect = new TrustedRedirectResponse($url, 302);
      $redirect->send();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    $events[KernelEvents::REQUEST][] = array('checkRedirect');
    return $events;
  }

}
