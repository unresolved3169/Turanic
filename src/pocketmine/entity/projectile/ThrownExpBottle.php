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

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\level\particle\SpellParticle;
use pocketmine\nbt\tag\CompoundTag;

class ThrownExpBottle extends Projectile {
	const NETWORK_ID = self::XP_BOTTLE;

	public $width = 0.25;
	public $height = 0.25;

	protected $gravity = 0.1;
	protected $drag = 0.15;

	private $hasSplashed = false;

	/**
	 * ThrownExpBottle constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 * @param Entity|null $shootingEntity
	 */
	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
		parent::__construct($level, $nbt, $shootingEntity);
	}

	public function splash(){
		if(!$this->hasSplashed){
			$this->hasSplashed = true;
			$this->getLevel()->addParticle(new SpellParticle($this, 46, 82, 153));
			if($this->getLevel()->getServer()->expEnabled){
				$this->getLevel()->dropExperience($this->add(0, -0.2, 0), mt_rand(1, 4));
				$this->getLevel()->dropExperience($this->add(-0.1, -0.2, 0), mt_rand(1, 4));
				$this->getLevel()->dropExperience($this->add(0, -0.2, -0.1), mt_rand(1, 3));
			}

			$this->flagForDespawn();
		}
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate(int $currentTick){
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);

		$this->age++;

		if($this->age > 1200 or $this->isCollided){
			$this->splash();
			$hasUpdate = true;
		}

		$this->timings->stopTiming();

		return $hasUpdate;
	}
}