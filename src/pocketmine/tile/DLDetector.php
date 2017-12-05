<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\tile;

use pocketmine\block\Block;
use pocketmine\block\DaylightDetector;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class DLDetector extends Spawnable {
	private $lastType = 0;

	/**
	 * DLDetector constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->scheduleUpdate();
	}

	/**
	 * @return int
	 */
	public function getLightByTime(){
		$time = $this->getLevel()->getTime();
		if(($time >= Level::TIME_DAY and $time <= Level::TIME_SUNSET) or
			($time >= Level::TIME_SUNRISE and $time <= Level::TIME_FULL)
		) return 15;
		return 0;
	}

	/**
	 * @return bool
	 */
	public function isActivated() : bool{
		if($this->getType() == Block::DAYLIGHT_SENSOR){
		    return $this->getLightByTime() == 15 ? true : false;
		}else{
            return $this->getLightByTime() == 0 ? true : false;
		}
	}

	/**
	 * @return int
	 */
	private function getType() : int{
		return $this->getBlock()->getId();
	}

	/**
	 * @return bool
	 */
	public function onUpdate(){
		if(($this->getLevel()->getServer()->getTick() % 3) == 0){ //Update per 3 ticks
			if($this->getType() != $this->lastType){ //Update when changed
				/** @var DaylightDetector $block */
				$block = $this->getBlock();
				$this->lastType = $block->getId();
				$this->level->updateAroundRedstone($this);
			}
		}
		return true;
	}

	/**
	 * @return CompoundTag
	 */
	public function getSpawnCompound(){
		return new CompoundTag("", [
			new StringTag("id", Tile::DAY_LIGHT_DETECTOR),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
		]);
	}
}