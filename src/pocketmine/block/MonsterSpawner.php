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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\Player;
use pocketmine\tile\MobSpawner;
use pocketmine\tile\Tile;

class MonsterSpawner extends Solid {

	protected $id = self::MONSTER_SPAWNER;

	/**
	 * MonsterSpawner constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return 5;
	}

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Monster Spawner";
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

	/**
	 * @param Item        $item
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null){
		if($this->getDamage() == 0){
			if($item->getId() == Item::SPAWN_EGG){
				$tile = $this->getLevel()->getTileAt($this->x, $this->y, $this->z);
				if(!($tile instanceof MobSpawner)){
                    $tile = Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), MobSpawner::createNBT($this));
				}
				$this->meta = $item->getDamage();
				$tile->setEntityId($item->getDamage());
				return true;
			}
		}
		return false;
	}

	/**
	 * @param Item        $item
	 * @param Block       $block
	 * @param Block       $target
	 * @param int         $face
	 * @param float       $fx
	 * @param float       $fy
	 * @param float       $fz
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){

		$this->getLevel()->setBlock($block, $this, true, true);
		Tile::createTile(Tile::MOB_SPAWNER, $this->getLevel(), MobSpawner::createNBT($this, $face, $item, $player));
		return true;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
        $tile = $this->getLevel()->getTileAt($this->x, $this->y, $this->z);
        if(!$tile instanceof MobSpawner) return [];
        if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0){
            return [
                [$this->id, $tile->getEntityId(), 1] // TODO : ADD NBT
            ];
        }else{
            return [];
        }
	}

	public function canHarvestWithHand(): bool{
        return false;
    }
}