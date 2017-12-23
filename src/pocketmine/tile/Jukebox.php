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

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\item\{MusicDisc, Item};

class Jukebox extends Spawnable {

    const TAG_RECORD = "record";
    const TAG_RECORD_ITEM = "recordItem";

    /** @var int */
    protected $record = MusicDisc::NO_RECORD;
    /** @var Item */
    protected $recordItem;

    public function __construct(Level $level, CompoundTag $nbt){
        $this->record = $nbt->getInt(self::TAG_RECORD, MusicDisc::NO_RECORD);

        if($nbt->hasTag(self::TAG_RECORD_ITEM, CompoundTag::class)){
            $this->recordItem = Item::nbtDeserialize($nbt->getCompoundTag(self::TAG_RECORD_ITEM));
        }else{
            $this->recordItem = Item::get(Block::AIR);
        }

        parent::__construct($level, $nbt);
    }
    
    public function getRecord() : int{
    	return $this->record;
    }
    
    public function setRecord(int $record){
    	$this->record = $record;
    }
    
    public function getRecordItem() : Item{
    	return $this->recordItem;
    }
    
    public function setRecordItem(Item $item){
    	$this->recordItem = $item;
    }

    public function saveNBT(){
        parent::saveNBT();
        $this->namedtag->setInt("record", $this->record);
        $this->namedtag->setTag($this->recordItem instanceof MusicDisc ? $this->recordItem->nbtSerialize() : (Item::get(0))->nbtSerialize());
    }
    
    public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool{
        if($nbt->getString("id") !== Tile::JUKEBOX){
            return false;
        }
        
        $this->namedtag = $nbt;
        return true;
    }

	public function addAdditionalSpawnData(CompoundTag $nbt){
	    $nbt->setInt(self::TAG_RECORD, $this->record);
	    $nbt->setTag($this->recordItem instanceof MusicDisc ? $this->recordItem->nbtSerialize(-1, self::TAG_RECORD_ITEM) : (Item::get(Block::AIR))->nbtSerialize(-1, self::TAG_RECORD_ITEM));
    }

}