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
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class Beacon extends Spawnable implements Nameable, InventoryHolder {
    use NameableTrait;

    const TAG_PRIMARY = "primary";
    const TAG_SECONDARY = "secondary";

    /** @var BeaconInventory */
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
		if(!$nbt->hasTag(self::TAG_PRIMARY)){
			$nbt->setInt(self::TAG_PRIMARY, 0);
		}
		if(!$nbt->hasTag(self::TAG_SECONDARY)){
			$nbt->setInt(self::TAG_SECONDARY, 0);
		}

        parent::__construct($level, $nbt);
        $this->inventory = new BeaconInventory($this);
        $this->scheduleUpdate();
	}

	public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setByte("isMovable", 1);
        $nbt->setTag($this->namedtag->getTag(self::TAG_PRIMARY));
        $nbt->setTag($this->namedtag->getTag(self::TAG_SECONDARY));

        if($this->hasName()) {
            $nbt->setTag($this->namedtag->getTag("CustomName"));
        }
    }

    public function getDefaultName(): string{
        return "Beacon";
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
		if($nbt->getString("id") !== Tile::BEACON){
			return false;
		}
		$this->namedtag->setInt(self::TAG_PRIMARY, $nbt->getInt(self::TAG_PRIMARY, 0));
		$this->namedtag->setInt(self::TAG_SECONDARY, $nbt->getInt(self::TAG_SECONDARY, 0));
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
			if($this->namedtag->hasTag(self::TAG_PRIMARY) && $this->namedtag->getInt(self::TAG_PRIMARY, 0) != 0){
				$id = $this->namedtag->getInt(self::TAG_PRIMARY);
			}else if($this->namedtag->hasTag(self::TAG_SECONDARY) && $this->namedtag->getInt(self::TAG_SECONDARY, 0) != 0){
				$id = $this->namedtag->getInt(self::TAG_SECONDARY);
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