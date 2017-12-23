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
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;

class FlowerPot extends Spawnable {

    const TAG_ITEM = "item";
    const TAG_ITEM_DATA = "mData";

	/**
	 * FlowerPot constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
        if(!$nbt->hasTag(self::TAG_ITEM, ShortTag::class)){
            $nbt->setShort(self::TAG_ITEM, 0, true);
        }
        if(!$nbt->hasTag(self::TAG_ITEM_DATA, IntTag::class)){
            $nbt->setInt(self::TAG_ITEM_DATA, 0, true);
        }
		parent::__construct($level, $nbt);
	}

    public function canAddItem(Item $item) : bool{
        if(!$this->isEmpty()){
            return false;
        }
        switch($item->getId()){
            /** @noinspection PhpMissingBreakStatementInspection */
            case Item::TALL_GRASS:
                if($item->getDamage() === 1){
                    return false;
                }
            case Item::SAPLING:
            case Item::DEAD_BUSH:
            case Item::DANDELION:
            case Item::RED_FLOWER:
            case Item::BROWN_MUSHROOM:
            case Item::RED_MUSHROOM:
            case Item::CACTUS:
                return true;
            default:
                return false;
        }
    }

    public function getItem() : Item{
        return Item::get($this->namedtag->getShort(self::TAG_ITEM), $this->namedtag->getInt(self::TAG_ITEM_DATA), 1);
    }

    public function setItem(Item $item){
        $this->namedtag->setShort(self::TAG_ITEM, $item->getId());
        $this->namedtag->setInt(self::TAG_ITEM_DATA, $item->getDamage());
        $this->onChanged();
    }

    public function removeItem(){
        $this->setItem(Item::get(Item::AIR, 0, 0));
    }

    public function isEmpty() : bool{
        return $this->getItem()->isNull();
    }

    public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setTag($this->namedtag->getTag(self::TAG_ITEM));
        $nbt->setTag($this->namedtag->getTag(self::TAG_ITEM_DATA));
    }

    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, $face = null, $item = null, $player = null){
        $nbt->setShort(self::TAG_ITEM, 0);
        $nbt->setInt(self::TAG_ITEM_DATA, 0);
    }
}