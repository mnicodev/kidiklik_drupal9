<?php


namespace Drupal\kidiklik_event\EventSubscriber;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\HttpResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;

class InitSubscriber implements EventSubscriberInterface
{

  public function checkRedirect(GetResponseEvent $event)
  {
    global $_SERVER;
   
    //

	  $request = $event->getRequest();
    
    $user_roles = \Drupal::currentUser()->getAccount()->getRoles();
	  $node = \Drupal::routeMatch()->getParameters()->get("node");
    $route_name = \Drupal::routeMatch()->getRouteName();
    $kidi_service = \Drupal::service('kidiklik.service');

    if($route_name === 'node.add' && $user_roles === 'anonymous') {
      \Drupal::service('kidiklik.service')->banip();
    }
    /*if($kidi_service->hasRedirection()) {
      $redirect = new TrustedRedirectResponse($kidi_service->getRedirection());
      $redirect->send();
    }*/


    if($node === null) {
      $request_uri = $request->server->get('REQUEST_URI');
      preg_match('/\/(.*)\/([0-9]*)-(.*)/',$request_uri, $match);
      
      if(count($match)) {
        $node = Node::Load($match[2]);
      }
      
	  }
   
    if (!empty($node)) { 
      /*
       * test des pages dans le cas d'une erreur 404 
      */
     
      if($node->getTitle() === 'erreur 404' && !in_array($node->getType(), ['article', 'activite', 'agenda'])) {
        $request_uri = $request->server->get('REQUEST_URI');
        preg_match('/\/(.*)\/([0-9]*)-(.*)/',$request_uri, $match);
        
        if(count($match)) {
          $test_node = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
            'field_ref_activite' => $match[2],
            'type' => 'activite',
            'field_departement' => get_term_departement()
          ]));
          // test activite
          if(!empty($test_node)) {
            $redirect = new RedirectResponse($test_node->url(), 301);
            $redirect->send();
            exit;
          } else {
            // test agenda
            $test_node = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
              'field_ref_agenda' => $match[2],
              'type' => 'agenda',
              'field_departement' => get_term_departement()
            ]));
            if(!empty($test_node)) {
              $redirect = new RedirectResponse($test_node->url(), 301);
              $redirect->send();
              exit;
            } else {
              // test artcile
              $test_node = current(\Drupal::entityTypeManager()->getStorage("node")->loadByProperties([
                'field_ref_entite' => $match[2],
                'type' => 'article',
                'field_departement' => get_term_departement()
              ]));
              if(!empty($test_node)) {
                $redirect = new RedirectResponse($test_node->url(), 301);
                $redirect->send();
                exit;
              }
            }
          }
          
        }
      }
           
      
      if (in_array($node->getType(), ['client', 'adherent']) && strstr($request->getPathInfo(), 'edit') === false) {
        $redirect = new RedirectResponse('/');
        $redirect->send();
      }
      
      if($node->__isset('field_departement') && (bool)$node->get('field_departement')->getValue() !== false) {
        $dep_node = (int)\Drupal::entityTypeManager()
          ->getStorage("taxonomy_term")
          ->load((int)$node->get('field_departement')->first()->getString())->getName();
        
      }
     
     // kint($dep_node);exit;
      if(\Drupal::routeMatch()->getRouteName() === 'entity.node.canonical') {
        /* on redirige vers la racine si on affiche un contenu de mise en avant */
        if($node->getType() === 'bloc_de_mise_en_avant') {
          $redirect = new RedirectResponse('/');
          $redirect->send();
          exit;
        }

        if(!empty($dep_node)) {

          $globalSettings = \Drupal::service("settings");
          $domain = $globalSettings->get("domain_name");
          
          
          if($dep_node !== (int)get_departement() && in_array($node->getType(), ['agenda', 'activite','article'])) {
            if($dep_node === 0) {
              
              $dep_node = 'www';
              
              if($globalSettings->get('environment') === 'dev') {
                $dep_node = '';
                
              }
  
            } elseif($dep_node < 10) {
              $dep_node = '0'.$dep_node;
            } 
            
            $url_redirect = sprintf('https://%s.%s%s', $dep_node, $domain, $node->url());
            
            $response_headers = [
              'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ];
            
            $redirect = new TrustedRedirectResponse($url_redirect, 302, $response_headers);
            $redirect->addCacheableDependency($node);
            $redirect->send();
            exit;
          }
        }
      }


      if((in_array('administrateur_de_departement', $user_roles) && \Drupal::routeMatch()->getRouteName() === 'entity.node.edit_form' && !in_array((int)$kidi_service->getDepartement(), $kidi_service->getUserDepartement()))) {
        if(!in_array('administrator', $user_roles)) {
            drupal_set_message(t("Vous n'êtes pas autorisé à éditer cette page"), 'error');
            $redirect = new RedirectResponse('/admin');
            $redirect->send();
            exit;
        }
      }
      
    } 


    preg_match("/admin/", $request->getRequestUri(), $rs);

    if (count($rs) > 0 && !in_array('administrator', \Drupal::currentUser()->getAccount()->getRoles())) {
        $term_dep = (int)current(user_load(\Drupal::currentUser()->getAccount()->id())->get('field_departement')->getValue())['target_id'];
        //kint($kidi_service->getDepartement());        kint($kidi_service->getUserDepartement());exit;
        //kint(\Drupal::currentUser()->getAccount()->getRoles());exit;
        //if ($term_dep !== (int)$kidi_service->getTermDepartement()) {
        if(!in_array((int)$kidi_service->getDepartement(), $kidi_service->getUserDepartement())) {
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
        'name' => $kidi_service->getDepartement(),
        'vid' => 'departement'
      ]))
      ->get("status")
      ->getValue());

    if (!(int)$dep_status["value"] && $kidi_service->getDepartement() !== 0) {

      $tab = explode(".", \Drupal::request()->getHost());
      array_shift($tab);
      $url = "http://www." . implode(".", $tab) . "/kidiklik-recrute.html";

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
