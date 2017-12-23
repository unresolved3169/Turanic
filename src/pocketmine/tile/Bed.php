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

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;

class Bed extends Spawnable {

    const TAG_COLOR = "color";

	/**
	 * Bed constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag(self::TAG_COLOR, ByteTag::class)){
			$nbt->setByte(self::TAG_COLOR, 14, true); //default to old red
		}
		parent::__construct($level, $nbt);
	}

	/**
	 * @return int
	 */
	public function getColor() : int{
		return $this->namedtag->getByte(self::TAG_COLOR);
	}

	/**
	 * @param int $color
	 */
	public function setColor(int $color){
		$this->namedtag->setByte(self::TAG_COLOR, $color & 0x0f);
		$this->onChanged();
	}

    public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setTag($this->namedtag->getTag(self::TAG_COLOR));
    }

    /**
     * @param CompoundTag $nbt
     * @param Vector3 $pos
     * @param null $face
     * @param Item|null $item
     * @param null $player
     */
    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, $face = null, $item = null, $player = null){
        $nbt->setByte(self::TAG_COLOR, $item !== null ? $item->getDamage() : 14); //default red
    }

}