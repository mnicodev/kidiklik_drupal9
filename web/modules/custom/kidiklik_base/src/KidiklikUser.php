<?php

namespace Drupal\kidiklik_base;

use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;

class KidiklikUser
{


  static function getAccount()
  {
    return \Drupal::currentUser()->GetAccount();
  }


  static function getDepartement()
  {
    $user = User::Load(\Drupal::currentUser()->id());
    $deps = $user->get('field_departement')->getValue();
    $term = null;
    $ids = [];
    foreach($deps as $dep)  {
      $ids[]= $dep['target_id'];
    }
    if(count($ids)) {
      $term = Term::loadMultiple($ids);
      $output = [];
      foreach($term as $dep) {
        $output[] = $dep->getName();
      }
    }

    return $output;
  }
}
