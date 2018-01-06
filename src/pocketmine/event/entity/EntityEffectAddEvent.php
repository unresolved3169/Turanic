<?php

/*
 *
 *    _______                    _
 *   |__   __|                  (_)
 *      | |_   _ _ __ __ _ _ __  _  ___
 *      | | | | | '__/ _` | '_ \| |/ __|
 *      | | |_| | | | (_| | | | | | (__
 *      |_|\__,_|_|  \__,_|_| |_|_|\___|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Turanic
 *
 */

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;

class EntityEffectAddEvent extends EntityEvent implements Cancellable {

	public static $handlerList = null;

	/** @var Effect */
	protected $effect;
    protected $oldEffect;

    /**
     * EntityEffectAddEvent constructor.
     *
     * @param Entity $entity
     * @param Effect $effect
     * @param Effect $oldEffect
     */
	public function __construct(Entity $entity, Effect $effect, Effect $oldEffect){
		$this->entity = $entity;
		$this->effect = $effect;
		$this->oldEffect = $oldEffect;
	}

	/**
	 * @return Effect
	 */
	public function getEffect(){
		return $this->effect;
	}

    /**
     * @return bool
     */
    public function hasOldEffect() : bool{
        return $this->oldEffect instanceof Effect;
    }

    /**
     * @return Effect|null
     */
    public function getOldEffect(){
        return $this->oldEffect;
    }
}
