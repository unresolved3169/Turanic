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
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;

// TODO : OPTIMIZE
class VirtualHolder extends Chest {

    protected $inventory;
    protected $cevir;

    public function __construct(Player $o, $name = "Virtual"){
        if(($o->y - 2) <= 0){
            return false;
        }
        parent::__construct($o->level, new CompoundTag("", [
            new StringTag("id", Tile::VIRTUAL_HOLDER),
            new StringTag("CustomName", $name),
            new IntTag("x", (int) $o->x),
            new IntTag("y", (int) $o->y - 2),
            new IntTag("z", (int) $o->z)]));
        $this->inventory = new VirtualInventory($this);
        $this->cevir = Block::get($this->getBlock()->getId(), $this->getBlock()->getDamage());

        $pk = new UpdateBlockPacket();
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->blockId = 54;
        $pk->blockData = 0;
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $o->dataPacket($pk);
        $this->spawnTo($o);
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

    public function spawnToAll(){
    	
    }
}