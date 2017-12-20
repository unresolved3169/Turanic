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

namespace pocketmine\entity\object;

use pocketmine\entity\Vehicle;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;

class Boat extends Vehicle {
	const NETWORK_ID = self::BOAT;

	public $height = 0.7;
	public $width = 1.6;

	public $gravity = 0.5;
	public $drag = 0.1;

	/**
	 * Boat constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->WoodID)){
			$nbt->WoodID = new IntTag("WoodID", 0);
		}
		parent::__construct($level, $nbt);
		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $this->getWoodID());
	}

	/**
	 * @return int
	 */
	public function getWoodID() : int{
		return (int) $this->namedtag["WoodID"];
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = Boat::NETWORK_ID;
        $pk->position = $this->getPosition();
        $pk->motion = $this->getMotion();
		$pk->yaw = 0;
		$pk->pitch = 0;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

    /**
     * @param EntityDamageEvent $source
     * @return bool|void
     * @internal param float $damage
     */
	public function attack(EntityDamageEvent $source){
		parent::attack($source);

		if(!$source->isCancelled()){
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->id;
			$pk->event = EntityEventPacket::HURT_ANIMATION;
			foreach($this->getLevel()->getPlayers() as $player){
				$player->dataPacket($pk);
			}
		}
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate(int $currentTick){
		if($this->closed){
			return false;
		}
		$tickDiff = $currentTick - $this->lastUpdate;
		if($tickDiff <= 0 and !$this->justCreated){
			return true;
		}

		$this->lastUpdate = $currentTick;

		$this->timings->startTiming();

		$hasUpdate = $this->entityBaseTick($tickDiff);

		if(!$this->level->getBlock(new Vector3($this->x, $this->y, $this->z))->getBoundingBox() == null or $this->isInsideOfWater()){
			$this->motionY = 0.1;
		}else{
			$this->motionY = -0.08;
		}

		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->updateMovement();

		if($this->linkedEntity == null or $this->linkedType = 0){
			if($this->age > 1500){
				$this->close();
				$hasUpdate = true;
				//$this->scheduleUpdate();

				$this->age = 0;
			}
			$this->age++;
		}else $this->age = 0;

		$this->timings->stopTiming();


		return $hasUpdate or !$this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
	}


	/**
	 * @return array
	 */
	public function getDrops(){
		return [
			ItemItem::get(ItemItem::BOAT, 0, 1)
		];
	}

	/**
	 * @return string
	 */
	public function getSaveId(){
		$class = new \ReflectionClass(static::class);
		return $class->getShortName();
	}
}
