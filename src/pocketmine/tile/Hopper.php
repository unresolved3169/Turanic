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

use pocketmine\block\Hopper as HopperBlock;
use pocketmine\entity\object\Item as DroppedItem;
use pocketmine\inventory\HopperInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;

class Hopper extends Spawnable implements InventoryHolder, Container, Nameable {
    use NameableTrait, ContainerTrait;

    const TAG_TRANSFER_COOLDOWN = "TransferCooldown";

	/** @var HopperInventory */
	protected $inventory;

	/** @var bool */
	protected $isLocked = false;

	/** @var bool */
	protected $isPowered = false;

	/**
	 * Hopper constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag(self::TAG_TRANSFER_COOLDOWN, IntTag::class)){
			$nbt->setInt(self::TAG_TRANSFER_COOLDOWN, 0);
		}

		parent::__construct($level, $nbt);
		$this->inventory = new HopperInventory($this);
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

	public function activate(){
		$this->isPowered = true;
	}

	public function deactivate(){
		$this->isPowered = false;
	}

	/**
	 * @return bool
	 */
	public function canUpdate(){
		return $this->namedtag->getInt(self::TAG_TRANSFER_COOLDOWN) === 0 and !$this->isPowered;
	}

	public function resetCooldownTicks(){
		$this->namedtag->setInt(self::TAG_TRANSFER_COOLDOWN, 8);
	}

	/**
	 * @return bool
	 */
	public function onUpdate(){
		if(!($this->getBlock() instanceof HopperBlock)){
			return false;
		}
		//Pickup dropped items
		//This can happen at any time regardless of cooldown
		$area = clone $this->getBlock()->getBoundingBox(); //Area above hopper to draw items from
		$area->maxY = ceil($area->maxY) + 1; //Account for full block above, not just 1 + 5/8
		foreach($this->getLevel()->getChunkEntities($this->getBlock()->x >> 4, $this->getBlock()->z >> 4) as $entity){
			if(!($entity instanceof DroppedItem) or !$entity->isAlive()){
				continue;
			}
			if(!$entity->boundingBox->intersectsWith($area)){
				continue;
			}

			$item = $entity->getItem();
			if(!$item instanceof Item){
				continue;
			}
			if($item->getCount() < 1){
				$entity->kill();
				continue;
			}

			if($this->inventory->canAddItem($item)){
				$this->inventory->addItem($item);
				$entity->kill();
			}
		}

		if(!$this->canUpdate()){ //Hoppers only update CONTENTS every 8th tick
			$this->namedtag->setInt(self::TAG_TRANSFER_COOLDOWN, $this->namedtag->getInt(self::TAG_TRANSFER_COOLDOWN) - 1);
			return true;
		}

		//Suck items from above tile inventories
		$source = $this->getLevel()->getTile($this->getBlock()->getSide(Vector3::SIDE_UP));
		if($source instanceof Tile and $source instanceof InventoryHolder){
			$inventory = $source->getInventory();
			$item = clone $inventory->getItem($inventory->firstEmpty());
			$item->setCount(1);
			if($this->inventory->canAddItem($item)){
				$this->inventory->addItem($item);
				$inventory->removeItem($item);
				$this->resetCooldownTicks();
				if($source instanceof Hopper){
					$source->resetCooldownTicks();
				}
			}
		}

		//Feed item into target inventory
		//Do not do this if there's a hopper underneath this hopper, to follow vanilla behaviour
		if(!($this->getLevel()->getTile($this->getBlock()->getSide(Vector3::SIDE_DOWN)) instanceof Hopper)){
			$target = $this->getLevel()->getTile($this->getBlock()->getSide($this->getBlock()->getDamage()));
			if($target instanceof Tile and $target instanceof InventoryHolder){
				$inv = $target->getInventory();
				foreach($this->inventory->getContents() as $item){
					if($item->getId() === Item::AIR or $item->getCount() < 1){
						continue;
					}
					$targetItem = clone $item;
					$targetItem->setCount(1);

					if($inv->canAddItem($targetItem)){
						$inv->addItem($targetItem);
						$this->inventory->removeItem($targetItem);
						$this->resetCooldownTicks();
						if($target instanceof Hopper){
							$target->resetCooldownTicks();
						}
						break;
					}

				}
			}
		}

		return true;
	}

	/**
	 * @return HopperInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	public function getRealInventory(){
        return $this->inventory;
    }

    /**
	 * @return int
	 */
	public function getSize(){
		return 5;
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->saveItems();
	}

	public function getDefaultName(): string{
        return "Hopper";
    }


    /**
	 * @return bool
	 */
	public function hasLock(){
		return $this->namedtag->hasTag("Lock");
	}

	/**
	 * @param string $itemName
	 */
	public function setLock(string $itemName = ""){
		if($itemName === ""){
			$this->namedtag->removeTag("Lock");
			return;
		}
		$this->namedtag->setString("Lock", $itemName);
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function checkLock(string $key){
		return $this->namedtag->getString("Lock") === $key;
	}

	public function addAdditionalSpawnData(CompoundTag $nbt){
        if($this->hasName()){
            $nbt->setTag($this->namedtag->getTag("CustomName"));
        }
        if($this->hasLock()){
            $nbt->setTag($this->namedtag->getTag("Lock"));
        }
    }
}
