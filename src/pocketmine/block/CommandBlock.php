<?php

/*
 *
 *  _____            _               _____           
 * / ____|          (_)             |  __ \          
 *| |  __  ___ _ __  _ ___ _   _ ___| |__) | __ ___  
 *| | |_ |/ _ \ '_ \| / __| | | / __|  ___/ '__/ _ \ 
 *| |__| |  __/ | | | \__ \ |_| \__ \ |   | | | (_) |
 * \_____|\___|_| |_|_|___/\__, |___/_|   |_|  \___/ 
 *                         __/ |                    
 *                        |___/                     
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Turanic
 * @link https://github.com/Turanic/Turanic
 *
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\tile\CommandBlock as TileCB;

class CommandBlock extends Solid {
	protected $id = self::COMMAND_BLOCK;

	/**
	 * CommandBlock constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Command Block";
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return -1;
	}

	public function isBreakable(Item $item){
        return false;
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
        if(!($player instanceof Player) && !$player->isOp() && !$player->isCreative()){
            return false;
        }
        $pitch = $player->pitch;
        if (abs($pitch) >= 60) {
            if ($pitch < 0) {
                $f = 4;
            } else {
                $f = 5;
            }
        } else {
            $f = ($player->getDirection() - 1) & 0x03;
        }
        $faces = [
            0 => 4,
            1 => 2,
            2 => 5,
            3 => 3,
            4 => 0,
            5 => 1
        ];
        $this->meta = $faces[$f];
        $this->level->setBlock($this, $this);
        $nbt = new CompoundTag("", [
            new StringTag("id", Tile::COMMAND_BLOCK),
            new IntTag("x", $this->x),
            new IntTag("y", $this->y),
            new IntTag("z", $this->z),
            new IntTag("blockType", $this->getBlockType())
        ]);
        Tile::createTile(Tile::COMMAND_BLOCK, $this->level, $nbt);
        return true;
    }

    public function onActivate(Item $item, Player $player = null){
        if(!($player instanceof Player) or !$player->isOp() or !$player->isCreative()){
            return false;
        }
        $tile = $this->getTile();
        if(!$tile instanceof TileCB){
            $nbt = new CompoundTag("", [
                new StringTag("id", Tile::COMMAND_BLOCK),
                new IntTag("x", $this->x),
                new IntTag("y", $this->y),
                new IntTag("z", $this->z),
                new IntTag("blockType", $this->getBlockType())
            ]);
            $tile = Tile::createTile(Tile::COMMAND_BLOCK, $this->level, $nbt);
        }
        $tile->spawnTo($player);
        $tile->show($player);
        return true;
    }

    public function activate(){
        $tile = $this->getTile();
        if($tile instanceof TileCB){
            $tile->setPowered(true);
        }
    }

    public function deactivate(){
        $tile = $this->getTile();
        if($tile instanceof TileCB){
            $tile->setPowered(false);
        }
    }

    public function getBlockType() : int{
        return TileCB::NORMAL;
    }

    public function getTile(){
        return $this->level->getTile($this);
    }

    public function getResistance(){
        return 18000000;
    }
}
