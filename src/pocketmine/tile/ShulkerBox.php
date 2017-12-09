<?php

/*
 *
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
 *
*/

namespace pocketmine\tile;

use pocketmine\inventory\ShulkerBoxInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;

class ShulkerBox extends Spawnable implements InventoryHolder, Container, Nameable {

    protected $inventory;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $this->inventory = new ShulkerBoxInventory($this);
        if(!isset($this->namedtag->Items) or !($this->namedtag->Items instanceof ListTag)){
            $this->namedtag->Items = new ListTag("Items", []);
            $this->namedtag->Items->setTagType(NBT::TAG_Compound);
        }
        for($i = 0; $i < $this->getSize(); ++$i){
            $this->inventory->setItem($i, $this->getItem($i), false);
        }
    }

    protected function getSlotIndex($index){
        foreach($this->namedtag->Items as $i => $slot){
            if((int) $slot["Slot"] === (int) $index){
                return (int) $i;
            }
        }

        return -1;
    }

    /**
     * @param int $index
     *
     * @return Item
     */
    public function getItem($index){
        $i = $this->getSlotIndex($index);
        if($i < 0){
            return Item::get(Item::AIR, 0, 0);
        }else{
            return Item::nbtDeserialize($this->namedtag->Items[$i]);
        }
    }

    /**
     * @param int $index
     * @param Item $item
     */
    public function setItem($index, Item $item){
        $i = $this->getSlotIndex($index);

        if($item->getId() === Item::AIR or $item->getCount() <= 0){
            if($i >= 0){
                unset($this->namedtag->Items[$i]);
            }
        }elseif($i < 0){
            for($i = 0; $i <= $this->getSize(); ++$i){
                if(!isset($this->namedtag->Items[$i])){
                    break;
                }
            }
            $this->namedtag->Items[$i] = $item->nbtSerialize($index);
        }else{
            $this->namedtag->Items[$i] = $item->nbtSerialize($index);
        }
    }

    /**
     * @return int
     */
    public function getSize(){
        return 27;
    }

    /**
     * Get the object related inventory
     *
     * @return Inventory
     */
    public function getInventory(){
        return $this->inventory;
    }

    /**
     * @param string $str
     */
    public function setName(string $str){
        if($str === ""){
            unset($this->namedtag->CustomName);
            return;
        }

        $this->namedtag->CustomName = new StringTag("CustomName", $str);
    }

    /**
     * @return bool
     */
    public function hasName(){
        return isset($this->namedtag->CustomName);
    }

    public function saveNBT(){
        parent::saveNBT();
        $this->namedtag->Items = new ListTag("Items", []);
        $this->namedtag->Items->setTagType(NBT::TAG_Compound);
        for($index = 0; $index < $this->getSize(); ++$index){
            $this->setItem($index, $this->inventory->getItem($index));
        }
    }

    /**
     * @return CompoundTag
     */
    public function getSpawnCompound(){
        $c = new CompoundTag("", [
            new StringTag("id", Tile::CHEST),
            new IntTag("x", (int) $this->x),
            new IntTag("y", (int) $this->y),
            new IntTag("z", (int) $this->z)
        ]);

        if(isset($this->namedtag->Items)){
            $c->Items = $this->namedtag->Items;
        }

        if($this->hasName()){
            $c->CustomName = $this->namedtag->CustomName;
        }

        return $c;
    }

    public function close(){
        if($this->closed === false){
            foreach($this->getInventory()->getViewers() as $player){
                $player->removeWindow($this->getInventory());
            }
            parent::close();
        }
    }
}