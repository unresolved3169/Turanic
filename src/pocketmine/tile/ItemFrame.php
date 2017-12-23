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
use pocketmine\nbt\tag\FloatTag;

class ItemFrame extends Spawnable {

	public $map_uuid = -1;

    const TAG_ITEM_ROTATION = "ItemRotation";
	const TAG_ITEM_DROP_CHANCE = "ItemDropChance";
	const TAG_ITEM = "Item";

	/**
	 * ItemFrame constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag(self::TAG_ITEM_ROTATION, ByteTag::class)){
			$nbt->setByte(self::TAG_ITEM_ROTATION, 0);
		}

		if(!$nbt->hasTag(self::TAG_ITEM_DROP_CHANCE, FloatTag::class)){
			$nbt->setFloat(self::TAG_ITEM_DROP_CHANCE, 1.0);
		}

		parent::__construct($level, $nbt);
	}

	/**
	 * @return bool
	 */
	public function hasItem() : bool{
        return !$this->getItem()->isNull();
	}

	/**
	 * @return Item
	 */
	public function getItem() : Item{
        $c = $this->namedtag->getCompoundTag(self::TAG_ITEM);
        if($c !== null){
            return Item::nbtDeserialize($c);
        }

        return Item::get(Item::AIR, 0, 0);
	}

	/**
	 * @param Item|null $item
	 */
	public function setItem(Item $item = null){
        if($item !== null and !$item->isNull()){
            $this->namedtag->setTag($item->nbtSerialize(-1, self::TAG_ITEM));
        }else{
            $this->namedtag->removeTag(self::TAG_ITEM);
        }
		$this->onChanged();
	}

    public function getItemRotation() : int{
        return $this->namedtag->getByte(self::TAG_ITEM_ROTATION);
    }

    public function setItemRotation(int $rotation){
        $this->namedtag->setByte(self::TAG_ITEM_ROTATION, $rotation);
        $this->onChanged();
    }

    public function getItemDropChance() : float{
        return $this->namedtag->getFloat(self::TAG_ITEM_DROP_CHANCE);
    }

    public function setItemDropChance(float $chance){
        $this->namedtag->setFloat(self::TAG_ITEM_DROP_CHANCE, $chance);
        $this->onChanged();
    }

	/**
	 * @param int $mapid
	 */
	public function setMapID(int $mapid){
		$this->map_uuid = $mapid;
		$this->namedtag->setInt("map_uuid", $mapid);
		$this->onChanged();
	}

	/**
	 * @return int
	 */
	public function getMapID() : int{
		return $this->map_uuid;
	}

    public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setTag($this->namedtag->getTag(self::TAG_ITEM_DROP_CHANCE));
        $nbt->setTag($this->namedtag->getTag(self::TAG_ITEM_ROTATION));
        if($this->hasItem()){
            $nbt->setTag($this->namedtag->getTag(self::TAG_ITEM));
        }
    }

    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, $face = null, $item = null, $player = null){
        $nbt->setFloat(self::TAG_ITEM_DROP_CHANCE, 1.0);
        $nbt->setByte(self::TAG_ITEM_ROTATION, 0);
    }

}