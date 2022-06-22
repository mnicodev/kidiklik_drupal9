<?php

namespace Drupal\kidiklik_event\Event;

use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;

class NodeInsertEvent extends Event {
	const NODE_INSERT = "kidiklik_event.node.insert";

	protected $entity;


	/**
	* Constructs a node insertion demo event object.
	*
	* @param \Drupal\Core\Entity\EntityInterface $entity
	*/
	public function __construct(EntityInterface $entity) {
		$this->entity=$entity;

	}

	/**
	 * Get the inserted entity.
	 *
	 * @return \Drupal\Core\Entity\EntityInterface
	 */
	public function getEntity() {
		return $this->entity;
	}
}

