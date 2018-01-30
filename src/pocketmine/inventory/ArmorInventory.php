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

namespace pocketmine\inventory;

use pocketmine\entity\Living;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\Player;
use pocketmine\Server;

class ArmorInventory extends BaseInventory{
    const SLOT_HEAD = 0;
    const SLOT_CHEST = 1;
    const SLOT_LEGS = 2;
    const SLOT_FEET = 3;

    /** @var Living */
    protected $holder;

    public function __construct(Living $holder){
        $this->holder = $holder;
        parent::__construct();
    }

    public function getHolder() : Living{
        return $this->holder;
    }

    public function getName() : string{
        return "Armor";
    }

    public function getDefaultSize() : int{
        return 4;
    }

    public function getHelmet() : Item{
        return $this->getItem(self::SLOT_HEAD);
    }

    public function getChestplate() : Item{
        return $this->getItem(self::SLOT_CHEST);
    }

    public function getLeggings() : Item{
        return $this->getItem(self::SLOT_LEGS);
    }

    public function getBoots() : Item{
        return $this->getItem(self::SLOT_FEET);
    }

    public function setHelmet(Item $helmet) : bool{
        return $this->setItem(self::SLOT_HEAD, $helmet);
    }

    public function setChestplate(Item $chestplate) : bool{
        return $this->setItem(self::SLOT_CHEST, $chestplate);
    }

    public function setLeggings(Item $leggings) : bool{
        return $this->setItem(self::SLOT_LEGS, $leggings);
    }

    public function setBoots(Item $boots) : bool{
        return $this->setItem(self::SLOT_FEET, $boots);
    }

    /**
     * @param int $index
     * @param Item $newItem
     * @return null|Item
     */
    protected function doSetItemEvents(int $index, Item $newItem){
        Server::getInstance()->getPluginManager()->callEvent($ev = new EntityArmorChangeEvent($this->getHolder(), $this->getItem($index), $newItem, $index));
        if($ev->isCancelled()){
            return null;
        }

        return $ev->getNewItem();
    }

    public function sendSlot(int $index, $target){
        if($target instanceof Player){
            $target = [$target];
        }

        $armor = $this->getContents(true);

        $pk = new MobArmorEquipmentPacket();
        $pk->entityRuntimeId = $this->getHolder()->getId();
        $pk->slots = $armor;
        $pk->encode();

        foreach($target as $player){
            if($player === $this->getHolder()){
                /** @var Player $player */

                $pk2 = new InventorySlotPacket();
                $pk2->windowId = $player->getWindowId($this);
                $pk2->inventorySlot = $index - $this->getSize();
                $pk2->item = $this->getItem($index);
                $player->dataPacket($pk2);
            }else{
                $player->dataPacket($pk);
            }
        }
    }

    public function sendContents($target){
        if($target instanceof Player){
            $target = [$target];
        }

        $armor = $this->getContents(true);

        $pk = new MobArmorEquipmentPacket();
        $pk->entityRuntimeId = $this->getHolder()->getId();
        $pk->slots = $armor;
        $pk->encode();

        foreach($target as $player){
            $player->dataPacket($pk);
        }
    }

    /**
     * @return Player[]
     */
    public function getViewers() : array{
        return array_merge(parent::getViewers(), $this->holder->getViewers());
    }
}