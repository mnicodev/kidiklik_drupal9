<?php

namespace Drupal\kidiklik_base;

use Drupal\kidiklik_base\KidiklikUser;
use Drupal\user\Entity\User;

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
    if($this->request_uri === '/') {
	    $this->request_uri = null;
    }
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

  public function getPageDepartement($dep = null) {
    if ($dep === null) {
      $dep = $this->getDepartement();
    }

    $term_dep = "";

    $term_dep = current(\Drupal::entityTypeManager()
      ->getStorage("taxonomy_term")
      ->loadByProperties(['name' => $dep]));
    if (!empty($term_dep)) {
      return $term_dep;
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

  public function getUser() {
    return User::Load(KidiklikUser::getAccount()->Id());
  }

  public function getUserDepartement() {
    return KidiklikUser::getDepartement();
  }

  public function getInformationsVille($ville) {
    $database = \Drupal::database();
    $query = $database->query('SELECT * FROM villes where commune = :ville', [
      ':ville' => $ville
    ]);
    $result = $query->fetchAll();
    return current($result);
  }

  public function getInformationsVillesByCP($cp) {
    $database = \Drupal::database();
    $query = $database->query('SELECT * FROM villes where code_postal like :cp', [
      ':cp' => $cp.'%'
    ]);
    $result = $query->fetchAll();
    return current($result);
  }

  public function searchAsDepartement($val) {
    $database = \Drupal::database();
    $query = $database->query('select * from taxonomy_term__field_nom where field_nom_value like :term', [
      ':term' => $val
    ]);
    return $query->fetch();
  }

  public function searchAsRegion($val) {
    $database = \Drupal::database();
    $query = $database->query('select * from taxonomy_term__field_region where field_region_value like :term', [
      ':term' => $val
    ]);
    return $query->fetch();
  }

  public function searchFromVille($val) {
    $database = \Drupal::database();
    $query = $database->query('select * from villes where (commune like :ou1 or commune like :ou2) and substr(code_postal, 1, 2) = :ou3', [
      ':ou1' => $val,
      ':ou2' => str_replace(' ','-',$val),
      ':ou3' => $this->getDepartement(),
    ]);
    return $query->fetch();
  }

  public function searchFromCp($val) {
    $database = \Drupal::database();
    $query = $database->query('select * from villes where code_postal = :ou3', [
      ':ou3' => $val,
    ]);
    return $query->fetch();
  }
}
