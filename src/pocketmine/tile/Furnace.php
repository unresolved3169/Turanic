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
use pocketmine\block\BlockFactory;
use pocketmine\event\inventory\FurnaceBurnEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\inventory\FurnaceInventory;
use pocketmine\inventory\FurnaceRecipe;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;

class Furnace extends Spawnable implements InventoryHolder, Container, Nameable {
    use NameableTrait, ContainerTrait;

	/** @var FurnaceInventory */
	protected $inventory;

    const TAG_BURN_TIME = "BurnTime";
	const TAG_COOK_TIME = "CookTime";
	const TAG_MAX_TIME = "MaxTime";
	const TAG_BURN_TICKS = "BurnTicks";

	/**
	 * Furnace constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
        if(!$nbt->hasTag(self::TAG_BURN_TIME, ShortTag::class) or $nbt->getShort(self::TAG_BURN_TIME) < 0){
            $nbt->setShort(self::TAG_BURN_TIME, 0, true);
        }

        if(
            !$nbt->hasTag(self::TAG_COOK_TIME, ShortTag::class) or
            $nbt->getShort(self::TAG_COOK_TIME) < 0 or
            ($nbt->getShort(self::TAG_BURN_TIME) === 0 and $nbt->getShort(self::TAG_COOK_TIME) > 0)
        ){
            $nbt->setShort(self::TAG_COOK_TIME, 0, true);
        }

        if(!$nbt->hasTag(self::TAG_MAX_TIME, ShortTag::class)){
            $nbt->setShort(self::TAG_MAX_TIME, $nbt->getShort(self::TAG_BURN_TIME), true);
            $nbt->removeTag(self::TAG_BURN_TICKS);
        }

        if(!$nbt->getTag(self::TAG_BURN_TICKS, ShortTag::class)){
            $nbt->setShort(self::TAG_BURN_TICKS, 0, true);
        }

		parent::__construct($level, $nbt);
		$this->inventory = new FurnaceInventory($this);
		$this->loadItems();

        if($this->namedtag->getShort(self::TAG_BURN_TIME) > 0){
			$this->scheduleUpdate();
		}
	}

	public function getDefaultName(): string{
        return "Furnace";
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
		return 3;
	}

	/**
	 * @return FurnaceInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	/**
	 * @param Item $fuel
	 */
	protected function checkFuel(Item $fuel){
		$this->server->getPluginManager()->callEvent($ev = new FurnaceBurnEvent($this, $fuel, $fuel->getFuelTime()));

		if($ev->isCancelled()){
			return;
		}

        $this->namedtag->setShort(self::TAG_MAX_TIME, $ev->getBurnTime());
        $this->namedtag->setShort(self::TAG_BURN_TIME, $ev->getBurnTime());
        $this->namedtag->setShort(self::TAG_BURN_TICKS, 0);
		if($this->getBlock()->getId() === Block::FURNACE){
			$this->getLevel()->setBlock($this, BlockFactory::get(Block::BURNING_FURNACE, $this->getBlock()->getDamage()), true);
		}

        if($this->namedtag->getShort(self::TAG_BURN_TIME) > 0 and $ev->isBurning()){
            $fuel->pop();
            $this->inventory->setFuel($fuel);
        }
	}

	/**
	 * @return bool
	 */
	public function onUpdate(){
        if($this->closed === true){
            return false;
        }

        $this->timings->startTiming();

        $ret = false;

        $fuel = $this->inventory->getFuel();
        $raw = $this->inventory->getSmelting();
        $product = $this->inventory->getResult();
        $smelt = $this->server->getCraftingManager()->matchFurnaceRecipe($raw);
        $canSmelt = ($smelt instanceof FurnaceRecipe and $raw->getCount() > 0 and (($smelt->getResult()->equals($product) and $product->getCount() < $product->getMaxStackSize()) or $product->isNull()));

        if($this->namedtag->getShort(self::TAG_BURN_TIME) <= 0 and $canSmelt and $fuel->getFuelTime() > 0 and $fuel->getCount() > 0){
            $this->checkFuel($fuel);
        }

        if($this->namedtag->getShort(self::TAG_BURN_TIME) > 0){
            $this->namedtag->setShort(self::TAG_BURN_TIME, $this->namedtag->getShort(self::TAG_BURN_TIME) - 1);
            $this->namedtag->setShort(self::TAG_BURN_TICKS, (int) ceil($this->namedtag->getShort(self::TAG_BURN_TIME) / $this->namedtag->getShort(self::TAG_MAX_TIME) * 200));
            if($smelt instanceof FurnaceRecipe and $canSmelt){
                $this->namedtag->setShort(self::TAG_COOK_TIME, $this->namedtag->getShort(self::TAG_COOK_TIME) + 1);
                if($this->namedtag->getShort(self::TAG_COOK_TIME) >= 200){ //10 seconds
                    $product = Item::get($smelt->getResult()->getId(), $smelt->getResult()->getDamage(), $product->getCount() + 1);
                    $this->server->getPluginManager()->callEvent($ev = new FurnaceSmeltEvent($this, $raw, $product));
                    if(!$ev->isCancelled()){
                        $this->inventory->setResult($ev->getResult());
                        $raw->pop();
                        $this->inventory->setSmelting($raw);
                    }
                    $this->namedtag->setShort(self::TAG_COOK_TIME, $this->namedtag->getShort(self::TAG_COOK_TIME) - 200);
                }
            }elseif($this->namedtag->getShort(self::TAG_BURN_TIME) <= 0){
                $this->namedtag->setShort(self::TAG_BURN_TIME, 0);
                $this->namedtag->setShort(self::TAG_COOK_TIME, 0);
                $this->namedtag->setShort(self::TAG_BURN_TICKS, 0);
            }else{
                $this->namedtag->setShort(self::TAG_COOK_TIME, 0);
            }
            $ret = true;
        }else{
            if($this->getBlock()->getId() === Block::BURNING_FURNACE){
                $this->getLevel()->setBlock($this, BlockFactory::get(Block::FURNACE, $this->getBlock()->getDamage()), true);
            }
            $this->namedtag->setShort(self::TAG_BURN_TIME, 0);
            $this->namedtag->setShort(self::TAG_COOK_TIME, 0);
            $this->namedtag->setShort(self::TAG_BURN_TICKS, 0);
        }

        foreach($this->getInventory()->getViewers() as $player){
            $windowId = $player->getWindowId($this->getInventory());
            if($windowId > 0){
                $pk = new ContainerSetDataPacket();
                $pk->windowId = $windowId;
                $pk->property = ContainerSetDataPacket::PROPERTY_FURNACE_TICK_COUNT; //Smelting
                $pk->value = $this->namedtag->getShort(self::TAG_COOK_TIME);
                $player->dataPacket($pk);
                $pk = new ContainerSetDataPacket();
                $pk->windowId = $windowId;
                $pk->property = ContainerSetDataPacket::PROPERTY_FURNACE_LIT_TIME;
                $pk->value = $this->namedtag->getShort(self::TAG_BURN_TICKS);
                $player->dataPacket($pk);
            }
        }

        $this->timings->stopTiming();

        return $ret;
	}

    public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setTag($this->namedtag->getTag(self::TAG_BURN_TIME));
        $nbt->setTag($this->namedtag->getTag(self::TAG_COOK_TIME));
        if($this->hasName()){
            $nbt->setTag($this->namedtag->getTag("CustomName"));
        }
    }

    /**
     * @param CompoundTag $nbt
     * @param Vector3 $pos
     * @param null $face
     * @param Item|null $item
     * @param null $player
     */
    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, $face = null, $item = null, $player = null){
        $nbt->setTag(new ListTag("Items", [], NBT::TAG_Compound));
        if($item !== null and $item->hasCustomName()){
            $nbt->setString("CustomName", $item->getCustomName());
        }
    }

    /**
     * @return Inventory
     */
    public function getRealInventory(){
        return $this->inventory;
    }
}
