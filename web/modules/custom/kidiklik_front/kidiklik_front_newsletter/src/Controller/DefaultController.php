<?php

namespace Drupal\kidiklik_front_newsletter\Controller;
require "vendor/autoload.php";
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use \Mailjet\Resources;

/**
 * Class DefaultController.
 */
class DefaultController extends ControllerBase {

  /**
   * Send.
   *
   * @return string
   *   Return Hello string.
   */
  public function send() {
    $email = filter_input(INPUT_GET, 'email');
    $code_dept = filter_input(INPUT_GET, 'dept');
    $apiKey= 'cbe190b2ccf97ebe0d109fb5fad89e37';
	  $secretKey= 'dee8bb0254e9ed326dcd3f36a698cc69';
    
     try {
			$mj=new \Mailjet\Client($apiKey,$secretKey);
      
			/* récupéparation des listes de contact */
			$filters=array("limit"=>100);
			$response=$mj->get(Resources::$Contactslist,array('filters'=>$filters));
			$response->success();
		
			$liste=$response->getData();
			
			
			$ID="";
			$le_dept=$code_dept;
			if($le_dept === '01') {
				$le_dept = '01-bis';
			}
			foreach($liste as $item) {
				if($item["Name"]=="LC_".$le_dept) {
					// on récupére l'id de la liste
					$ID=$item["ID"];
					break;
				}
			}

			// si l'ID est null, alors on crée la liste de contact
			if(empty($ID)) {
				$filters=array("Name"=>$le_dept."b");
				$response=$mj->post(Resources::$Contactslist,array('body'=>$filters));
				if($response->success()) {
					$liste=current($response->getData());
					$ID=$liste["ID"];
				}
			}

			// on crée le contact
			$body=array("Email" => $email);

			$response=$mj->post(Resources::$Contact,array('body'=>$body));
   
			if($response->success()) {
				$contact=current($response->getData());
				$body=array('ContactsLists' => array(
						array('ListID' => $ID, 'Action' => "addforce"),
					)
				);
				$response=$mj->post(Resources::$ContactManagecontactslists,array('id'=>$contact["ID"],'body'=>$body));

				if(!$response->success()) {
					$res = new Response();
					return $res->setContent($response->getBody()['ErrorMessage']);
					//return $response->getBody()['ErrorMessage'];
				} else {
					$response = new Response();
					return $response->setContent('ok');
				}
			} else {
        		$res = new Response();
				return $res->setContent($response->getBody()['ErrorMessage']);
			}

	} catch(Exception $e) {
		$response = new Response();
    	return $response->setContent('nok');
	}

  }

}
