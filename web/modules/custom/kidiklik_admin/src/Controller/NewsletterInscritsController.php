<?php

namespace Drupal\kidiklik_admin\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class NewsletterInscritsController.
 */
class NewsletterInscritsController extends ControllerBase {

  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
  public function list() {
    $start = \Drupal::request()->get('start');
    $step = \Drupal::request()->get('step');
    if(empty($start)) {
      $start = 0;
    }
    if(empty($step)) {
      $step= 20;
    }

    $database=\Drupal::database();
    $sql = "select * from  inscrits_newsletters where dept='".get_departement()."' order by inscription desc";
    $query=$database->query($sql);
    $list = $query->fetchAll();
    $taille = count($list) - 20;

    $sql = "select * from  inscrits_newsletters where dept='".get_departement()."' order by inscription desc limit $start, $step";
    $query=$database->query($sql);
    $list = $query->fetchAll();



     return [
      "#theme"=>"inscrits_newsletter",
      "#list"=>$list,
      "#prec" => ($start>0?$start - $step:0),
      "#suiv" => $start + $step,
      "#step" => $step,
       "#count_list" => $taille
    ];
  }

}
