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
    $term = Term::Load(current($user->get('field_departement')->getValue())['target_id']);
    return $term->getName();
  }
}
