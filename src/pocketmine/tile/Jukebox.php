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
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\item\{MusicDisc, Item};

class Jukebox extends Spawnable {

    protected $record = MusicDisc::NO_RECORD;
    protected $recordItem;

    public function __construct(Level $level, CompoundTag $nbt){
        if(isset($nbt->record)){
            $this->record = $nbt->record->getValue();
        }
        
        if(isset($nbt->recordItem)){
            $this->recordItem = Item::nbtDeserialize($nbt->recordItem->getValue());
        }

        parent::__construct($level, $nbt);
    }
    
    public function getRecord() : int{
    	return $this->record;
    }
    
    public function setRecord(int $record){
    	$this->record = $record;
    }
    
    public function getRecordItem(){
    	return $this->recordItem;
    }
    
    public function setRecordItem($item = null){
    	$this->recordItem = $item;
    }

    public function saveNBT(){
        parent::saveNBT();
        $this->namedtag->record = new IntTag("record", $this->record);
        $this->namedtag->recordItem = ($this->recordItem instanceof MusicDisc ? $this->recordItem->nbtSerialize() : (Item::get(0))->nbtSerialize());
    }
    
    public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool{
        if($nbt["id"] !== Tile::JUKEBOX){
            return false;
        }
        
        $this->namedtag = $nbt;
        return true;
    }

	/**
	 * @return CompoundTag
	 */
	public function getSpawnCompound(){
		return new CompoundTag("", [
			new StringTag("id", Tile::JUKEBOX),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			new IntTag("record", $this->record),
			($this->recordItem instanceof MusicDisc ? $this->recordItem->nbtSerialize() : (Item::get(0))->nbtSerialize())
		]);
	}

}