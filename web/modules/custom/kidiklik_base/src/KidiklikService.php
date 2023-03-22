<?php

namespace Drupal\kidiklik_base;

/**
 * Class KidiklikService.
 */
class KidiklikService  {
  private $server_name;
  private $dep;
  private $redirections;
  private $domain_name;
  private $request_uri;

  /**
   * Constructs a new KidiklikService object.
   */
  public function __construct() {
    $globalSettings = \Drupal::service("settings");
    $this->server_name = \Drupal::request()->server->get('SERVER_NAME');
    $tmp = explode('.', $this->server_name);
    if(!empty($tmp) && count($tmp)>0) {
      $this->dep = $tmp[0];
    }
    $this->redirections = $globalSettings->get('redirections');
    $this->domain_name = $globalSettings->get('domain_name');
    $this->request_uri = \Drupal::request()->server->get('REQUEST_URI') ?? \Drupal::request()->server->get('REDIRECT_URL') ?? \Drupal::request()->server->get('SCRIPT_URL');
  }

  /**
   * 
   */
  public function getDepartement() {
    $globalSettings = \Drupal::service("settings");
    $dep = $globalSettings->get("dep");
  
    return $dep;
  }

  /**
   * 
   */
  public function getTermDepartement($dep = null, $option = null)
  {
    if ($dep === null) {
      $dep = $this->getDepartement();
    }
  
    $term_dep = "";
  
    $term_dep = current(\Drupal::entityTypeManager()
      ->getStorage("taxonomy_term")
      ->loadByProperties(['name' => $dep]));
    if (!empty($term_dep)) {
      if ($option === 'name') {
        return $term_dep->getName();
      }
      return $term_dep->id();
    }
    return null;
  }

  public function hasRedirection() 
  {
    return array_key_exists($this->dep, $this->redirections);
  }

  public function getRedirection() {
    if(array_key_exists($this->dep, $this->redirections)) {
      return sprintf('https://%s.%s/%s', $this->redirections[$this->dep], $this->domain_name, $this->request_uri);
    }
    return false;    
  }
}
