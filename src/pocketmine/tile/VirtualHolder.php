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
use pocketmine\inventory\VirtualInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;

class VirtualHolder extends Spawnable implements InventoryHolder, Container, Nameable {
    use NameableTrait, ContainerTrait;

    protected $inventory;
    protected $cevir;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        $this->inventory = new VirtualInventory($this);
        $this->loadItems();
        $this->cevir = Block::get($this->getBlock()->getId(), $this->getBlock()->getDamage());
        return $this;
    }

    public function getInventory(){
        return $this->inventory;
    }

    public function cevir(Player $o){
        $blok = $this->cevir;
        $blok->setComponents($this->getFloorX(), $this->getFloorY(), $this->getFloorZ());
        $blok->level = $this->getLevel();
        if($blok->level !== null){
            $blok->level->sendBlocks([$o], [$blok]);
        }
    }

    public function spawnTo(Player $player){
        $pk = new UpdateBlockPacket();
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->blockId = 54;
        $pk->blockData = 0;
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $player->dataPacket($pk);
        return parent::spawnTo($player);
    }

    public function spawnToAll(){
    	
    }

    /**
     * @return int
     */
    public function getSize(): int {
        return 27;
    }

    /**
     * @return VirtualInventory
     */
    public function getRealInventory(){
        return $this->inventory;
    }

    /**
     * @return string
     */
    public function getDefaultName(): string{
        return "Turanic Virtual Holder";
    }

    public function addAdditionalSpawnData(CompoundTag $nbt){
        if($this->hasName()) {
            $nbt->setTag($this->namedtag->getTag("CustomName"));
        }
    }

    public function saveNBT(){
        parent::saveNBT();
        $this->saveItems();
    }

    /**
     * @param CompoundTag $nbt
     * @param Vector3 $pos
     * @param null $face
     * @param Item|null $item
     * @param null $player
     */
    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, $face = null, $item = null, $player = null){
        $nbt->setTag(new ListTag(Container::TAG_ITEMS, [], NBT::TAG_Compound));
        if($item !== null and $item->hasCustomName()){
            $nbt->setString("CustomName", $item->getCustomName());
        }
    }
}