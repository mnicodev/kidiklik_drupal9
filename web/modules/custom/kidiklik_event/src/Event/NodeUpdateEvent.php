<?php

namespace Drupal\kidiklik_event\Event;

use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;

class NodeUpdateEvent extends Event {
	const NODE_UPDATE = "kidiklik_event.node.update";

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

