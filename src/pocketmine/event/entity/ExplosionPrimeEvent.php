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

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;

/**
 * Called when a entity decides to explode
 */
class ExplosionPrimeEvent extends EntityEvent implements Cancellable {
	public static $handlerList = null;

	/** @var float */
	protected $force;
	/** @var bool */
	private $blockBreaking = true;
	/** @var bool */
	private $dropItem;

	/**
	 * @param Entity $entity
	 * @param float  $force
	 * @param bool   $dropItem
	 */
	public function __construct(Entity $entity, float $force, bool $dropItem = true){
		$this->entity = $entity;
		$this->force = $force;
		$this->dropItem = $dropItem;
	}

	/**
	 * @param bool $dropItem
	 */
	public function setDropItem(bool $dropItem){
		$this->dropItem = $dropItem;
	}

	/**
	 * @return bool
	 */
	public function dropItem() : bool{
		return $this->dropItem;
	}

	/**
	 * @return float
	 */
	public function getForce() : float{
		return $this->force;
	}

	/**
	 * @param $force
	 */
	public function setForce(float $force){
		$this->force = $force;
	}

	/**
	 * @return bool
	 */
	public function isBlockBreaking() : bool{
		return $this->blockBreaking;
	}

	/**
	 * @param bool $affectsBlocks
	 */
	public function setBlockBreaking(bool $affectsBlocks){
		$this->blockBreaking = $affectsBlocks;
	}

}