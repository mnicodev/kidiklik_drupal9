<?php

require "vendor/autoload.php";
use \Mailjet\Resources;

$email = filter_input(INPUT_GET, 'email');
$code_dept = filter_input(INPUT_GET, 'dept');

	$apiKey= 'cbe190b2ccf97ebe0d109fb5fad89e37';
	$secretKey= '6b396ab69ffd08cef2192aab002303ee';
	try {
			$mj=new \Mailjet\Client($apiKey,$secretKey);

			/* récupéparation des listes de contact */
			$filters=array("limit"=>100);
			$response=$mj->get(Resources::$Contactslist,array('filters'=>$filters));
			$response->success();
			$liste=$response->getData();
			
			//echo "<pre>";print_r($response);exit;
			$ID="";
			$le_dept=$code_dept;
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
    //var_dump($response->getBody());
			if($response->success()) {
				$contact=current($response->getData());
				$body=array('ContactsLists' => array(
						array('ListID' => $ID, 'Action' => "addforce"),
					)
				);
				$response=$mj->post(Resources::$ContactManagecontactslists,array('id'=>$contact["ID"],'body'=>$body));

				if(!$response->success()) echo $response->getBody()['ErrorMessage'];
				else echo "ok";
			} else {
        echo $response->getBody()['ErrorMessage'];
			}

	} catch(Exception $e) {
		echo "nok";
	}



