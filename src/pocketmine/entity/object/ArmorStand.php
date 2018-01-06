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

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\item\Item as ItemItem;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\Player;

class ArmorStand extends Entity {

    const NETWORK_ID = self::ARMOR_STAND;

    /** @var ItemItem */
    protected $handItem;
    protected $helmet;
    protected $chestplate;
    protected $leggings;
    protected $boots;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);

        $item = ItemItem::get(ItemItem::AIR)->nbtSerialize();
        if (!$nbt->hasTag("HandItems", ListTag::class)) {
            $nbt->setTag(new ListTag("HandItems", [
                $item, // main
                $item // off
            ], NBT::TAG_Compound));
        }

        if (!$nbt->hasTag("ArmorItems", ListTag::class)) {
            $nbt->setTag(new ListTag("ArmorItems", [
                $item, // boots
                $item, // leggings
                $item, // chestplate
                $item // helmet
            ], NBT::TAG_Compound));
		}

		$this->handItem = ItemItem::nbtDeserialize($nbt->getListTag("HandItems")[0]);
		$this->helmet = ItemItem::nbtDeserialize($nbt->getListTag("ArmorItems")[3]);
		$this->chestplate = ItemItem::nbtDeserialize($nbt->getListTag("ArmorItems")[2]);
		$this->leggings = ItemItem::nbtDeserialize($nbt->getListTag("ArmorItems")[1]);
		$this->boots = ItemItem::nbtDeserialize($nbt->getListTag("ArmorItems")[0]);

        $this->setHealth(2);
        $this->setMaxHealth(2);
    }

    public function canCollideWith(Entity $entity) : bool{
        return false;
    }

    public function onUpdate(int $currentTick){
        if(parent::onUpdate($currentTick)){
            $v = $this->getSide(self::SIDE_DOWN);
            if($this->level->getBlock($v, false)->getId() == ItemItem::AIR){
                $this->setMotion($v);
            }
            return true;
        }
        return false;
    }

    public function onInteract(Player $player, ItemItem $item){
        $change = null;
        if($item->getId() == ItemItem::AIR){
            $this->sendHandItem($player);
            $this->sendArmorItems($player);
            $player->getInventory()->sendContents($player);
        }
        if($item->isArmor()){
            if($item->isHelmet()){
                $change = $this->getHelmet();
                $this->setHelmet(clone $item);
            }elseif($item->isChestplate()){
                $change = $this->getChestplate();
                $this->setChestplate(clone $item);
            }elseif($item->isLeggings()){
                $change = $this->getLeggings();
                $this->setLeggings(clone $item);
            }elseif($item->isBoots()){
                $change = $this->getBoots();
                $this->setBoots(clone $item);
            }
        }else{
            if ($item->getCount() > 1) {
                $change = clone $item;
                $change->setCount($change->getCount() - 1);
                $player->getInventory()->setItemInHand($change);
                $player->getInventory()->addItem($this->getHandItem());
                $item->setCount(1);
                $this->setHandItem(clone $item);
                return false;
            } else {
                $change = $this->getHandItem();
                $this->setHandItem($item);
            }
        }
        $player->getInventory()->setItemInHand($change);
        return false;
    }

    public function kill(){
        $this->level->dropItem($this, ItemItem::get(ItemItem::ARMOR_STAND));
        $this->level->dropItem($this, $this->getHandItem());
        $this->level->dropItem($this, $this->getHelmet());
        $this->level->dropItem($this, $this->getChestplate());
        $this->level->dropItem($this, $this->getLeggings());
        $this->level->dropItem($this, $this->getBoots());
        parent::kill();
    }

    public function spawnTo(Player $player){
        parent::spawnTo($player);
        $this->sendArmorItems($player);
        $this->sendHandItem($player);
    }

    public function sendHandItem(Player $player) {
        $pk = new MobEquipmentPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->inventorySlot = $pk->hotbarSlot = 0;
        $pk->item = $this->getHandItem();
        $player->dataPacket($pk);
    }

    public function sendArmorItems(Player $player) {
        $pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->slots = [$this->getHelmet(), $this->getChestplate(), $this->getLeggings(), $this->getBoots()];
		$player->dataPacket($pk);
	}

	public function sendAll() {
        foreach ($this->level->getChunkPlayers($this->chunk->getX(), $this->chunk->getZ()) as $player) {
            if ($player->isOnline()) {
                $this->sendHandItem($player);
                $this->sendArmorItems($player);
            }
        }
	}

	public function saveNBT(){
        parent::saveNBT();

        $this->namedtag->setTag(new ListTag("ArmorItems", [
            $this->boots->nbtSerialize(),
            $this->leggings->nbtSerialize(),
            $this->chestplate->nbtSerialize(),
            $this->helmet->nbtSerialize()
        ], NBT::TAG_Compound));

        $this->namedtag->setTag(new ListTag("HandItems", [$this->handItem->nbtSerialize(), ItemItem::get(ItemItem::AIR)->nbtSerialize()], NBT::TAG_Compound));
    }

    public function getHandItem() : ItemItem{
        return $this->handItem;
    }

    public function setHandItem(ItemItem $item){
        $this->handItem = $item;
        $this->sendAll();
    }

    public function getHelmet() : ItemItem{
        return $this->helmet;
    }

    public function setHelmet(ItemItem $item){
        $this->helmet = $item;
        $this->sendAll();
    }

    public function getChestplate() : ItemItem{
        return $this->chestplate;
    }

    public function setChestplate(ItemItem $item){
        $this->chestplate = $item;
        $this->sendAll();
    }

    public function getLeggings() : ItemItem{
        return $this->leggings;
    }

    public function setLeggings(ItemItem $item){
        $this->leggings = $item;
        $this->sendAll();
    }

    public function getBoots() : ItemItem{
        return $this->boots;
    }

    public function setBoots(ItemItem $item){
        $this->boots = $item;
        $this->sendAll();
    }
}