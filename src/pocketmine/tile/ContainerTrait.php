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

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

/**
 * This trait implements most methods in the {@link Container} interface. It should only be used by Tiles.
 */
trait ContainerTrait{

    /**
     * @return int
     */
    abstract public function getSize() : int;

    abstract public function getNBT() : CompoundTag;

    /**
     * @return Inventory
     */
    abstract public function getRealInventory();

    /**
     * @param $index
     *
     * @return int
     */
    protected function getSlotIndex(int $index) : int{
        foreach($this->getNBT()->getListTag(Container::TAG_ITEMS) as $i => $slot){
            /** @var CompoundTag $slot */
            if($slot->getByte("Slot") === $index){
                return (int) $i;
            }
        }

        return -1;
    }

    /**
     * This method should not be used by plugins, use the Inventory
     *
     * @param int $index
     *
     * @return Item
     */
    public function getItem(int $index) : Item{
        $i = $this->getSlotIndex($index);
        /** @var CompoundTag|null $itemTag */
        $itemTag = $this->getNBT()->getListTag(Container::TAG_ITEMS)[$i] ?? null;
        if($itemTag !== null){
            return Item::nbtDeserialize($itemTag);
        }

        return Item::get(Item::AIR, 0, 0);
    }

    /**
     * This method should not be used by plugins, use the Inventory
     *
     * @param int  $index
     * @param Item $item
     */
    public function setItem(int $index, Item $item){
        $i = $this->getSlotIndex($index);

        $d = $item->nbtSerialize($index);

        $items = $this->getNBT()->getListTag(Container::TAG_ITEMS);
        assert($items instanceof ListTag);

        if($item->isNull()){
            if($i >= 0){
                unset($items[$i]);
            }
        }elseif($i < 0){
            for($i = 0; $i <= $this->getSize(); ++$i){
                if(!isset($items[$i])){
                    break;
                }
            }
            $items[$i] = $d;
        }else{
            $items[$i] = $d;
        }

        $this->getNBT()->setTag($items);
    }

    protected function loadItems(){
        if(!$this->getNBT()->hasTag(Container::TAG_ITEMS, ListTag::class)){
            $this->getNBT()->setTag(new ListTag(Container::TAG_ITEMS, [], NBT::TAG_Compound));
        }

        $inventory = $this->getRealInventory();
        for($i = 0, $size = $this->getSize(); $i < $size; ++$i){
            $inventory->setItem($i, $this->getItem($i));
        }
    }

    protected function saveItems(){
        $this->getNBT()->setTag(new ListTag(Container::TAG_ITEMS, [], NBT::TAG_Compound));

        $inventory = $this->getRealInventory();
        for($i = 0, $size = $this->getSize(); $i < $size; ++$i){
            $this->setItem($i, $inventory->getItem($i));
        }
    }
}