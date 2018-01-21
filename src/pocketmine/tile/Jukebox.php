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
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\Player;
use pocketmine\item\{MusicDisc, Item};
use pocketmine\Server;

class Jukebox extends Spawnable {

    const TAG_RECORD = "record";
    const TAG_RECORD_ITEM = "recordItem";

    /** @var int */
    protected $record = MusicDisc::NO_RECORD;
    /** @var Item */
    protected $recordItem;

    public function __construct(Level $level, CompoundTag $nbt){
        $this->record = $nbt->getInt(self::TAG_RECORD, MusicDisc::NO_RECORD);

        if(!$nbt->hasTag(self::TAG_RECORD_ITEM, CompoundTag::class)){
            $nbt->setTag((Item::get(Block::AIR))->nbtSerialize(-1, self::TAG_RECORD_ITEM));
        }

        $this->recordItem = Item::nbtDeserialize($nbt->getCompoundTag(self::TAG_RECORD_ITEM));

        parent::__construct($level, $nbt);
    }

    public function playMusicDisc(Player $player = null) : bool{
        $recordItem = $this->recordItem;
        if($recordItem instanceof MusicDisc){
            $soundId = $recordItem->getSoundId();
            if($soundId < 0) return false;

            $level = $this->level;
            $level->broadcastLevelSoundEvent($this, $soundId);
            // TODO : ADD translate
            Server::getInstance()->broadcastPopup("§dNow Playing : C418 - ".$recordItem->getRecordName(), $level->getNearestEntity($this, 8, Player::class)); // I don't find pink text color and I am not sure distance
            return true;
        }

        return false;
    }

    public function dropMusicDisc(){
        $this->level->dropItem($this->add(0.5,0.5,0.5), $this->recordItem);
        $this->recordItem = Item::get(Block::AIR);
    }

    public function setRecordItem(MusicDisc $disc){
        $this->recordItem = $disc;
        $this->record = $disc->getRecordId();
        $this->playMusicDisc();
    }

    public function onUpdate(){
        if($this->recordItem instanceof MusicDisc){
            $pk = new BlockEventPacket();
            $pk->x = $this->x;
            $pk->y = $this->y;
            $pk->z = $this->z;
            $pk->eventType = $this->recordItem->getSoundId();
            $pk->eventData = 0;
            $this->level->addChunkPacket($this->x >> 4, $this->z >> 4, $pk);
        }
    }

    public function saveNBT(){
        parent::saveNBT();
        $this->namedtag->setTag($this->recordItem->nbtSerialize(-1, self::TAG_RECORD_ITEM));
        $this->namedtag->setInt(self::TAG_RECORD, $this->record);
    }

    public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setInt(self::TAG_RECORD, $this->record);
        $ri = $this->recordItem instanceof Item ? $this->recordItem : Item::get(Block::AIR);
        $nbt->setTag($ri->nbtSerialize(-1, self::TAG_RECORD_ITEM));
    }

    public function updateCompoundTag(CompoundTag $nbt, Player $player): bool{
        $this->namedtag = $nbt;
        return true;
    }
}