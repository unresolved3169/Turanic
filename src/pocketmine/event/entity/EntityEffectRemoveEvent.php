<?php

namespace pocketmine\event\entity;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;

class EntityEffectRemoveEvent extends EntityEvent implements Cancellable {

	public static $handlerList = null;

	/** @var Effect */
	protected $effect;

    /**
     * EntityEffectRemoveEvent constructor.
     *
     * @param Entity $entity
     * @param Effect $effect
     */
	public function __construct(Entity $entity, Effect $effect){
		$this->entity = $entity;
		$this->effect = $effect;
	}

	/**
	 * @return Effect
	 */
	public function getEffect(){
		return $this->effect;
	}

	public function setCancelled($value = true){
        if($this->getEffect()->getDuration() <= 0){
            throw new \InvalidStateException("Removal of expired effects cannot be cancelled");
 		}
        parent::setCancelled($value);
    }
}
