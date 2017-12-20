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

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class Bed extends Spawnable {
	/**
	 * Bed constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->color) or !($nbt->color instanceof ByteTag)){
			$nbt->color = new ByteTag("color", 14); //default to old red
		}
		parent::__construct($level, $nbt);
	}

	/**
	 * @return int
	 */
	public function getColor() : int{
		return $this->namedtag->color->getValue();
	}

	/**
	 * @param int $color
	 */
	public function setColor(int $color){
		$this->namedtag["color"] = $color & 0x0f;
		$this->onChanged();
	}

	/**
	 * @return CompoundTag
	 */
	public function getSpawnCompound(){
		return new CompoundTag("", [
			new StringTag("id", Tile::BED),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			$this->namedtag->color
		]);
	}

}