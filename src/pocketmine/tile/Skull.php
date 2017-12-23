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
use pocketmine\Player;

class Skull extends Spawnable {

	const TYPE_SKELETON = 0;
	const TYPE_WITHER = 1;
	const TYPE_ZOMBIE = 2;
	const TYPE_HUMAN = 3;
	const TYPE_CREEPER = 4;
	const TYPE_DRAGON = 5;

    const TAG_SKULL_TYPE = "SkullType"; //TAG_Byte
    const TAG_ROT = "Rot"; //TAG_Byte
    const TAG_MOUTH_MOVING = "MouthMoving"; //TAG_Byte
    const TAG_MOUTH_TICK_COUNT = "MouthTickCount"; //TAG_Int

	/**
	 * Skull constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
        if(!$nbt->hasTag(self::TAG_SKULL_TYPE, ByteTag::class)){
            $nbt->setByte(self::TAG_SKULL_TYPE, 0, true);
        }
        if(!$nbt->hasTag(self::TAG_ROT, ByteTag::class)){
            $nbt->setByte(self::TAG_ROT, 0, true);
        }
		parent::__construct($level, $nbt);
	}

	public function setType(int $type){
        $this->namedtag->setByte(self::TAG_SKULL_TYPE, $type);
        $this->onChanged();
	}

	/**
	 * @return null
	 */
	public function getType(){
        return $this->namedtag->getByte(self::TAG_SKULL_TYPE);
	}

    public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setTag($this->namedtag->getTag(self::TAG_SKULL_TYPE));
        $nbt->setTag($this->namedtag->getTag(self::TAG_ROT));
    }

    /**
     * @param CompoundTag $nbt
     * @param Vector3 $pos
     * @param int|null $face
     * @param Item|null $item
     * @param Player|null $player
     */
    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, $face = null, $item = null, $player = null){
        $nbt->setByte(self::TAG_SKULL_TYPE, $item !== null ? $item->getDamage() : self::TYPE_SKELETON);
        $rot = 0;
        if($face === Vector3::SIDE_UP and $player !== null){
            $rot = floor(($player->yaw * 16 / 360) + 0.5) & 0x0F;
        }
        $nbt->setByte(self::TAG_ROT, $rot);
    }
}
