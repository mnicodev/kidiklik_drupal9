<?php

namespace Drupal\kidiklik_base;


class KidiklikEntity  {


	private $node;

	static function setGPS($node) {

		$ville=KidiklikEntity::getGPS($node->get("field_ville_save")->value);
		
		//kint($ville);exit;
		//kint($node->get("field_geolocalisation")->value);exit;
		if($ville) {
			$node->get("field_geolocalisation")->appendItem([
				"lat"=>$ville["lat"],
				"lng"=>$ville["lng"]
			]);
			$node->__set("field_latitude",$ville["lat"]);
			$node->__set("field_longitude",$ville["lng"]);
			$node->get('field_geolocation_demo_single')->delete();
			$node->get('field_geolocation_demo_single')->appendItem([
				"lat"=>$ville["lat"],
				"lng"=>$ville["lng"]
			]);
			return $node;
		}
	}

	static function getGPS($ville) {
		$database=\Drupal::database();
		$query=$database->query("select * from villes where commune=\"".html_entity_decode($ville)."\" limit 0,1");
		$rs=current($query->fetchAll());

		if(!empty($rs))
			return ["lat"=>$rs->lat,"lng"=>$rs->lng];
		else return false;
	}
}
