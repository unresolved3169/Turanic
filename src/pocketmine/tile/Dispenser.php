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
use pocketmine\inventory\DispenserInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class Dispenser extends Spawnable implements InventoryHolder, Container, Nameable {
    use NameableTrait, ContainerTrait;

	/** @var DispenserInventory */
	protected $inventory;

	/**
	 * Dispenser constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->inventory = new DispenserInventory($this);
		$this->loadItems();
		$this->scheduleUpdate();
	}

	public function close(){
		if($this->closed === false){
			$this->inventory->removeAllViewers(true);
			$this->inventory = null;
			parent::close();
		}
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->saveItems();
	}

	/**
	 * @return int
	 */
	public function getSize(){
		return 9;
	}

	/**
	 * @return DispenserInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	public function getDefaultName(): string{
        return "Dispenser";
    }

    /**
	 * @return array
	 */
	public function getMotion(){
		$meta = $this->getBlock()->getDamage();
		switch($meta){
			case Vector3::SIDE_DOWN:
				return [0, -1, 0];
			case Vector3::SIDE_UP:
				return [0, 1, 0];
			case Vector3::SIDE_NORTH:
				return [0, 0, -1];
			case Vector3::SIDE_SOUTH:
				return [0, 0, 1];
			case Vector3::SIDE_WEST:
				return [-1, 0, 0];
			case Vector3::SIDE_EAST:
				return [1, 0, 0];
			default:
				return [0, 0, 0];
		}
	}

	public function activate(){
		$itemIndex = [];
		for($i = 0; $i < $this->getSize(); $i++){
			$item = $this->getInventory()->getItem($i);
			if($item->getId() != Item::AIR){
				$itemIndex[] = [$i, $item];
			}
		}
		$max = count($itemIndex) - 1;
		if($max < 0) $itemArr = null;
		elseif($max == 0) $itemArr = $itemIndex[0];
		else $itemArr = $itemIndex[mt_rand(0, $max)];

		if(is_array($itemArr)){
			/** @var Item $item */
			$item = $itemArr[1];
			$item->setCount($item->getCount() - 1);
			$this->getInventory()->setItem($itemArr[0], $item->getCount() > 0 ? $item : Item::get(Item::AIR));
			$motion = $this->getMotion();
			$needItem = Item::get($item->getId(), $item->getDamage());
			$f = 1.5;
			$nbt = Entity::createBaseNBT(
			    new Vector3($this->x + $motion[0] * 2 + 0.5, $this->y + ($motion[1] > 0 ? $motion[1] : 0.5), $this->z + $motion[2] * 2 + 0.5),
                new Vector3(...$motion),
                lcg_value() * 360
            );
			switch($needItem->getId()){
				case Item::ARROW:
					$nbt->setShort("Fire", 0);
					$entity = Entity::createEntity("Arrow", $this->getLevel(), $nbt);
					break;
				case Item::SNOWBALL:
					$entity = Entity::createEntity("Snowball", $this->getLevel(), $nbt);
					break;
				case Item::EGG:
					$entity = Entity::createEntity("Egg", $this->getLevel(), $nbt);
					break;
				case Item::SPLASH_POTION:
					$nbt->setShort("PotionId", $item->getDamage());
					$entity = Entity::createEntity("ThrownPotion", $this->getLevel(), $nbt);
					break;
				case Item::ENCHANTING_BOTTLE:
					$entity = Entity::createEntity("ThrownExpBottle", $this->getLevel(), $nbt);
					break;
				default:
					$nbt->setShort("Health", 5);
					$nbt->setTag($needItem->nbtSerialize(-1, "Item"));
					$nbt->setShort("PickupDelay", 10);
					$f = 0.3;
					$entity = Entity::createEntity("Item", $this->getLevel(), $nbt);
					break;
			}

			$entity->setMotion($entity->getMotion()->multiply($f));
			$entity->spawnToAll();

			for($i = 1; $i < 10; $i++){
				$this->getLevel()->addParticle(new SmokeParticle($this->add($motion[0] * $i * 0.3 + 0.5, $motion[1] == 0 ? 0.5 : $motion[1] * $i * 0.3, $motion[2] * $i * 0.3 + 0.5)));
			}
		}
	}

	public function addAdditionalSpawnData(CompoundTag $nbt){
        if($this->hasName()){
            $nbt->setTag($this->namedtag->getTag("CustomName"));
        }
    }

    /**
     * @return Inventory
     */
    public function getRealInventory(){
        return $this->inventory;
    }
}