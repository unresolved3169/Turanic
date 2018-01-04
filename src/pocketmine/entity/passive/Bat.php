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

namespace pocketmine\entity\passive;

use pocketmine\entity\FlyingAnimal;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class Bat extends FlyingAnimal {

	const NETWORK_ID = self::BAT;

	const DATA_IS_RESTING = 16;

	public $width = 0.6;
	public $height = 0.6;

	public $flySpeed = 0.8;
	public $switchDirectionTicks = 100;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Bat";
	}

	public function initEntity(){
		$this->setMaxHealth(6);
		parent::initEntity();
	}

	/**
	 * Bat constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag("isResting")){
			$nbt->setByte("isResting", 0);
		}
		parent::__construct($level, $nbt);

		$this->setGenericFlag(self::DATA_FLAG_RESTING, (bool) $this->isResting());
	}

	/**
	 * @return int
	 */
	public function isResting() : int{
		return $this->namedtag->getByte("isResting");
	}

	/**
	 * @param bool $resting
	 */
	public function setResting(bool $resting){
		$this->namedtag->setByte("isResting", (int) $resting);
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate(int $currentTick){
		if($this->age > 20 * 60 * 10){
			$this->kill();
		}
		return parent::onUpdate($currentTick);
	}
}