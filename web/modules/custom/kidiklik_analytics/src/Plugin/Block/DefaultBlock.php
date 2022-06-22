<?php

namespace Drupal\kidiklik_analytics\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'DefaultBlock' block.
 *
 * @Block(
 *  id = "default_block",
 *  admin_label = @Translation("Google Analytics Data"),
 * )
 */
class DefaultBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	  
	  
		$dep=get_departement();
		/*$c=curl_init();
		$opt=[
			CURLOPT_URL=>"https://www.kidiklik.fr/load_data_analytics.php",
			CURLOPT_POST=>true,
			CURLOPT_RETURNTRANSFERT=>true,
			CURLOPT_POSTFIELDS=> [
				"dep"=>$dep,
			],
		];
		curl_setopt_array($c,$opt);
		$return=curl_exec($c);
		curl_close($c);*/
		//ksm($return);
    $build = [
		"#theme" => 'default_block',
		"#dep"=>$dep,
		"#url" => drupal_get_path("module","kidiklik_analytics")."/load_data.php",
		"#attached" => [
			"library" => ['kidiklik_analytics/kidiklik.analytics'],
		], 
    ];
    
    return $build;
  }

}
