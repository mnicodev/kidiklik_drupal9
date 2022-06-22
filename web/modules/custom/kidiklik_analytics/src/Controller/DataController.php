<?php

namespace Drupal\kidiklik_analytics\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DataController.
 */
class DataController extends ControllerBase {

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function content() {
	 $dep=get_departement();
	//$result=file_get_contents("https://www.kidiklik.fr/load_data_analytics.php?dep=".$dep);
	
	/*
	$c=curl_init();
	$opt=[
		CURLOPT_URL=>"https://www.kidiklik.fr/load_data_analytics.php",
		CURLOPT_GET=>true,
		CURLOPT_RETURNTRANSFERT=>true,
		CURLOPT_POSTFIELDS=> [
			"dep"=>$dep,
		],
	];
	curl_setopt_array($c,$opt);
	$return=curl_exec($c);
	curl_close($c);*/
	$result = [];
    return new JsonResponse(json_encode($result));
  }

}
