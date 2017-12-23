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

class EnchantTable extends Spawnable implements Nameable {
    use NameableTrait;

	/**
	 * EnchantTable constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
	}

	public function getDefaultName(): string{
        return "Enchanting Table";
    }

    /**
     * @param CompoundTag $nbt
     */
    public function addAdditionalSpawnData(CompoundTag $nbt){
        if($this->hasName()){
            $nbt->setTag($this->namedtag->getTag("CustomName"));
        }
    }

    /**
     * @param CompoundTag $nbt
     * @param Vector3 $pos
     * @param null $face
     * @param Item|null $item
     * @param null $player
     */
    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, $face = null, $item = null, $player = null){
        if($item !== null and $item->hasCustomName()){
            $nbt->setString("CustomName", $item->getCustomName());
        }
    }
}
