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

use pocketmine\inventory\BeaconInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;

class Beacon extends Spawnable implements Nameable, InventoryHolder {

	private $inventory;
	protected $currentTick = 0;
	const POWER_LEVEL_MAX = 4;

	/**
	 * Beacon constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->primary)){
			$nbt->setInt("primary", 0);
		}
		if(!isset($nbt->secondary)){
			$nbt->setInt("secondary", 0);
		}
		$this->inventory = new BeaconInventory($this);
		parent::__construct($level, $nbt);
		$this->scheduleUpdate();
	}

	public function saveNBT(){
		parent::saveNBT();
	}

	/**
	 * @return CompoundTag
	 */
	public function getSpawnCompound(){
		$c = new CompoundTag("", [
			new StringTag("id", Tile::BEACON),
			new ByteTag("isMovable", 1), // true
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			new IntTag("primary", $this->namedtag->getInt("primary")),
			new IntTag("secondary", $this->namedtag->getInt("secondary"))
		]);
		if($this->hasName()){
			$c->CustomName = $this->namedtag->CustomName;
		}
		return $c;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->hasName() ? $this->namedtag->CustomName->getValue() : "Beacon";
	}

	/**
	 * @return bool
	 */
	public function hasName(): bool{
		return isset($this->namedtag->CustomName);
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
	 * @return BeaconInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	/**
	 * @param CompoundTag $nbt
	 * @param Player      $player
	 *
	 * @return bool
	 */
	public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool{
		if($nbt["id"] !== Tile::BEACON){
			return false;
		}
		$this->namedtag->setInt("primary", $nbt->getInt("primary", 0));
		$this->namedtag->setInt("secondary", $nbt->getInt("secondary", 0));
		return true;
	}

	/**
	 * @return bool
	 */
	public function onUpdate(){
		if($this->closed === true){
			return false;
		}
		if($this->currentTick++ % 100 != 0){
			return true;
		}

		$level = $this->calculatePowerLevel();

		$this->timings->startTiming();

		$id = 0;

		if($level > 0){
			if($this->namedtag->hasTag("secondary") && $this->namedtag->getInt("primary", 0) != 0){
				$id = $this->namedtag->getInt("primary");
			}else if($this->namedtag->hasTag("secondary") && $this->namedtag->getInt("secondary", 0) != 0){
				$id = $this->namedtag->getInt("secondary");
			}
			if($id != 0){
				$range = ($level + 1) * 10;
				$effect = Effect::getEffect($id);
				$effect->setDuration(10 * 30);
				$effect->setAmplifier(0);
				foreach($this->level->getPlayers() as $player){
					if($this->distance($player) <= $range){
						$player->addEffect($effect);
					}
				}
			}
		}

		$this->timings->stopTiming();

		return true;
	}

	/**
	 * @return int
	 */
	protected function calculatePowerLevel(){
		$tileX = $this->getFloorX();
		$tileY = $this->getFloorY();
		$tileZ = $this->getFloorZ();
		for($powerLevel = 1; $powerLevel <= self::POWER_LEVEL_MAX; $powerLevel++){
			$queryY = $tileY - $powerLevel;
			for($queryX = $tileX - $powerLevel; $queryX <= $tileX + $powerLevel; $queryX++){
				for($queryZ = $tileZ - $powerLevel; $queryZ <= $tileZ + $powerLevel; $queryZ++){
					$testBlockId = $this->level->getBlockIdAt($queryX, $queryY, $queryZ);
					if(
						$testBlockId != Block::IRON_BLOCK &&
						$testBlockId != Block::GOLD_BLOCK &&
						$testBlockId != Block::EMERALD_BLOCK &&
						$testBlockId != Block::DIAMOND_BLOCK
					){
						return $powerLevel - 1;
					}
				}
			}
		}
		return self::POWER_LEVEL_MAX;
	}

}