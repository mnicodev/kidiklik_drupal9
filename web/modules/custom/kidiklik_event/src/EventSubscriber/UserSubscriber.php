<?php

namespace Drupal\kidiklik_event\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\entity\User;
use Drupal\Core\Access\AccessResult;

class UserSubscriber implements EventSubscriberInterface {

	public function checkUser(GetResponseEvent $event) {
		$user = \Drupal::entityTypeManager()->getStorage("user")->load(\Drupal::currentUser()->id());
		//kint($user);
		if($user->hasRole("administrateur_de_departement")) {
			$user_dep=(int)current(($user->get("field_departement")->getValue()))["target_id"];
			$url_dep=get_term_departement();

			if($user_dep!==$url_dep){
				$dep = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($url_dep);
			}

		}
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('checkUser');
    return $events;
  }

}
