<?php

Class Newssletter {
	private $entity;
	
	public function __construct() {
		
	}
	
	static public function load($nid) {
		\Drupal::entityTypeManager()
				->getStorage("node")
				->load(current($entity->get("field_newsletter")->getValue())["target_id"]);
	}
}
