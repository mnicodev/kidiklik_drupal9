<?php

namespace Drupal\kidiklik_newsletter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class BlocsDatasController.
 */
class BlocsDatasController extends ControllerBase {

  /**
   * Get.
   *
   * @return string
   *   Return Hello string.
   */
  public function get($nid) {
  	$node=\Drupal::entityTypeManager()->getStorage("node")->load($nid);

  	$liste_blocs=$node->get("field_blocs_de_donnees")->getValue();
  	$json=[];
  	foreach($liste_blocs as $item) {  	  
  		$paragraph=\Drupal\paragraphs\Entity\Paragraph::load($item["target_id"]);
		
		if(!empty($paragraph)) {
			$dept_target_id = current($paragraph->get("field_departement")->getValue())['target_id'];
			if(!empty($dept_target_id)) {
				//$term = \Drupal::entityTypeManager()->getStorage("taxonomy_term")->load($dept_target_id);
				if((int)$dept_target_id === (int)get_term_departement()) {
					if(!empty($paragraph->get("field_image")->getValue())) {
						$fid=current($paragraph->get("field_image")->getValue())["target_id"];
						$image=current(\Drupal::entityTypeManager()->getStorage("file")->load($fid));
						$url_image=file_create_url($image["uri"]["x-default"]);
					} else if(!empty($paragraph->get('field_image_save')->getValue())) {
						$url_image = current($paragraph->get('field_image_save')->getValue())['value'];
					} else {
						$url_image = null;
					}
					
					
					$json[]=[
						"titre"=>$paragraph->get("field_titre")->value,
						"resume"=>strip_tags($paragraph->get("field_resume")->value),
						"image"=>$url_image,
						"pid"=>$paragraph->id(),
						"fid"=>$fid,
						"lien" => $paragraph->get("field_lien")->value,
						"bloc_nid" => $paragraph->get("field_nid_bloc")->value,
						

					];
				}
			}
		}

  	}


    return new JsonResponse(json_encode($json));
  }

}
