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

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityGenerateEvent;
use pocketmine\item\Item;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;

class MobSpawner extends Spawnable {

    const TAG_ENTITY_ID = "EntityId";
    const TAG_SPAWN_COUNT = "SpawnCount";
    const TAG_SPAWN_RANGE = "SpawnRange";
    const TAG_MIN_SPAWN_DELAY = "MinSpawnDelay";
    const TAG_MAX_SPAWN_DELAY = "MaxSpawnDelay";
    const TAG_DELAY = "Delay";

	/**
	 * MobSpawner constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
	    // TODO : Optimize et ve yenile
		if(!$nbt->hasTag(self::TAG_ENTITY_ID, StringTag::class)){
			$nbt->setString(self::TAG_ENTITY_ID, "0");
		}
		if(!$nbt->hasTag(self::TAG_SPAWN_COUNT, ShortTag::class)){
			$nbt->setShort(self::TAG_SPAWN_COUNT, 4);
		}
        if(!$nbt->hasTag(self::TAG_SPAWN_RANGE, ShortTag::class)){
			$nbt->setShort(self::TAG_SPAWN_RANGE, 4);
		}
		if(!$nbt->hasTag(self::TAG_MIN_SPAWN_DELAY, ShortTag::class)){
			$nbt->setShort(self::TAG_MIN_SPAWN_DELAY, 200);
		}
		if(!$nbt->hasTag(self::TAG_MAX_SPAWN_DELAY, ShortTag::class)){
			$nbt->setShort(self::TAG_MAX_SPAWN_DELAY, 799);
		}
		if(!$nbt->hasTag(self::TAG_DELAY, ShortTag::class)){
			$nbt->setShort(self::TAG_DELAY, mt_rand($nbt->getShort(self::TAG_MIN_SPAWN_DELAY), $nbt->getShort(self::TAG_MAX_SPAWN_DELAY)));
		}
		parent::__construct($level, $nbt);
		if($this->getEntityId() > 0){
			$this->scheduleUpdate();
		}
	}

	/**
	 * @return int|null
	 */
	public function getEntityId(){
		return (int) $this->namedtag->getString(self::TAG_ENTITY_ID);
	}

	/**
	 * @param int $id
	 */
	public function setEntityId(int $id){
		$this->namedtag->setString(self::TAG_ENTITY_ID, "$id");
		$this->onChanged();
		$this->scheduleUpdate();
	}

	/**
	 * @return int
	 */
	public function getSpawnCount(){
		return $this->namedtag->getShort(self::TAG_SPAWN_COUNT);
	}

	/**
	 * @param int $value
	 */
	public function setSpawnCount(int $value){
		$this->namedtag->setShort(self::TAG_SPAWN_COUNT, $value);
	}

	/**
	 * @return int
	 */
	public function getSpawnRange(){
		return $this->namedtag->getShort(self::TAG_SPAWN_RANGE);
	}

	/**
	 * @param int $value
	 */
	public function setSpawnRange(int $value){
		$this->namedtag->setShort(self::TAG_SPAWN_RANGE, $value);
	}

	/**
	 * @return int
	 */
	public function getMinSpawnDelay(){
		return $this->namedtag->getShort(self::TAG_MIN_SPAWN_DELAY);
	}

	/**
	 * @param int $value
	 */
	public function setMinSpawnDelay(int $value){
		$this->namedtag->setShort(self::TAG_MIN_SPAWN_DELAY, $value);
	}

	/**
	 * @return int
	 */
	public function getMaxSpawnDelay(){
		return $this->namedtag->getShort(self::TAG_MAX_SPAWN_DELAY);
	}

	/**
	 * @param int $value
	 */
	public function setMaxSpawnDelay(int $value){
		$this->namedtag->setShort(self::TAG_MAX_SPAWN_DELAY, $value);
	}

	/**
	 * @return int
	 */
	public function getDelay(){
		return $this->namedtag->getShort(self::TAG_DELAY);
	}

	/**
	 * @param int $value
	 */
	public function setDelay(int $value){
		$this->namedtag->setShort(self::TAG_DELAY, $value);
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
	public function canUpdate() : bool{
		if($this->getEntityId() === 0) return false;
		$hasPlayer = false;
		$count = 0;
		foreach($this->getLevel()->getEntities() as $e){
			if($e instanceof Player){
				if($e->distance($this->getBlock()) <= 15) $hasPlayer = true;
			}
			if($e::NETWORK_ID == $this->getEntityId()){
				$count++;
			}
		}
		if($hasPlayer and $count < 15){ // Spawn limit = 15
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function onUpdate(){
		if($this->closed === true){
			return false;
		}

		$this->timings->startTiming();

		if(!($this->chunk instanceof Chunk))
			return false;

		if($this->canUpdate()){
			if($this->getDelay() <= 0){
				$success = 0;
				for($i = 0; $i < $this->getSpawnCount(); $i++){
					$pos = $this->add(mt_rand() / mt_getrandmax() * $this->getSpawnRange(), mt_rand(-1, 1), mt_rand() / mt_getrandmax() * $this->getSpawnRange());
					$target = $this->getLevel()->getBlock($pos);
					$ground = $target->getSide(Vector3::SIDE_DOWN);
					if($target->getId() == Item::AIR && $ground->isTopFacingSurfaceSolid()){
						$success++;
						$this->getLevel()->getServer()->getPluginManager()->callEvent($ev = new EntityGenerateEvent($pos, $this->getEntityId(), EntityGenerateEvent::CAUSE_MOB_SPAWNER));
						if(!$ev->isCancelled()){
                            $entity = Entity::createEntity($this->getEntityId(), $this->getLevel(), Entity::createBaseNBT($target->add(0.5, 0, 0.5), null, lcg_value() * 360, 0));
							$entity->spawnToAll();
						}
					}
				}
				if($success > 0){
					$this->setDelay(mt_rand($this->getMinSpawnDelay(), $this->getMaxSpawnDelay()));
				}
			}else{
				$this->setDelay($this->getDelay() - 1);
			}
		}

		$this->timings->stopTiming();

		return true;
	}

	public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setTag($this->namedtag->getTag(self::TAG_ENTITY_ID));
        $nbt->setTag($this->namedtag->getTag(self::TAG_DELAY));
        $nbt->setTag($this->namedtag->getTag(self::TAG_SPAWN_COUNT));
        $nbt->setTag($this->namedtag->getTag(self::TAG_SPAWN_RANGE));
        $nbt->setTag($this->namedtag->getTag(self::TAG_MIN_SPAWN_DELAY));
        $nbt->setTag($this->namedtag->getTag(self::TAG_MAX_SPAWN_DELAY));
    }
}
