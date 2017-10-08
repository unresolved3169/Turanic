<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

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
    	if($record > 511 or $record < 500){
    		return false;
    	}
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