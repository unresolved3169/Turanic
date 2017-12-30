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

use pocketmine\inventory\BrewingInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\Server;

class BrewingStand extends Spawnable implements InventoryHolder, Container, Nameable {
    use NameableTrait, ContainerTrait;

    const TAG_COOK_TIME = "CookTime";

	const MAX_BREW_TIME = 400;
	/** @var BrewingInventory */
	protected $inventory;

	public static $ingredients = [
		Item::NETHER_WART => 0,
		Item::GLOWSTONE_DUST => 0,
		Item::REDSTONE => 0,
		Item::FERMENTED_SPIDER_EYE => 0,

		Item::MAGMA_CREAM => 0,
		Item::SUGAR => 0,
		Item::GLISTERING_MELON => 0,
		Item::SPIDER_EYE => 0,
		Item::GHAST_TEAR => 0,
		Item::BLAZE_POWDER => 0,
		Item::GOLDEN_CARROT => 0,
		Item::PUFFER_FISH,
		Item::RABBIT_FOOT => 0,

		Item::GUNPOWDER => 0,
	];

	/**
	 * BrewingStand constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->CookedTime) or !($nbt->CookedTime instanceof ShortTag)){
			$nbt->CookedTime = new ShortTag("CookedTime", 0);
		}
		parent::__construct($level, $nbt);
		$this->inventory = new BrewingInventory($this);
		$this->loadItems();
	}

	public function getDefaultName(): string{
        return "Brewing Stand";
    }

    public function close(){
		if(!$this->closed){
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
		return 4;
	}

	/**
	 * @return BrewingInventory
	 */
	public function getInventory(){
		return $this->inventory;
	}

	public function getRealInventory(){
        return $this->inventory;
    }

    /**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function checkIngredient(Item $item){
		if(isset(self::$ingredients[$item->getId()])){
			if(self::$ingredients[$item->getId()] === $item->getDamage()){
				return true;
			}
		}
		return false;
	}

	public function updateSurface(){
		$this->saveNBT();
		$this->onChanged();
	}

    /**
     * @return bool
     * @throws \TypeError
     */
    public function onUpdate(){
		if($this->closed === true){
			return false;
		}

		$this->timings->startTiming();

		$ret = false;

		$ingredient = $this->inventory->getIngredient();
		$canBrew = false;

		for($i = 1; $i <= 3; $i++){
			if($this->inventory->getItem($i)->getId() === Item::POTION or
				$this->inventory->getItem($i)->getId() === Item::SPLASH_POTION
			){
				$canBrew = true;
			}
		}

		if(!$ingredient->isNull()){
			if($canBrew){
				if(!$this->checkIngredient($ingredient)){
					$canBrew = false;
				}
			}

			if($canBrew){
				for($i = 1; $i <= 3; $i++){
					$potion = $this->inventory->getItem($i);
					$recipe = Server::getInstance()->getCraftingManager()->matchBrewingRecipe($ingredient, $potion);
					if($recipe !== null){
						$canBrew = true;
						break;
					}
					$canBrew = false;
				}
			}
		}else{
			$canBrew = false;
		}

		if($canBrew){
		    $cookTime = $this->namedtag->getShort(self::TAG_COOK_TIME);

			$this->namedtag->setShort(self::TAG_COOK_TIME, $cookTime - 1);

			foreach($this->getInventory()->getViewers() as $player){
				$windowId = $player->getWindowId($this->getInventory());
				if($windowId > 0){
					$pk = new ContainerSetDataPacket();
					$pk->windowid = $windowId;
					$pk->property = 0; //Brew
					$pk->value = $cookTime;
					$player->dataPacket($pk);
				}
			}

			if($cookTime <= 0){
				$this->namedtag->setShort(self::TAG_COOK_TIME, self::MAX_BREW_TIME);
				for($i = 1; $i <= 3; $i++){
					$potion = $this->inventory->getItem($i);
					$recipe = Server::getInstance()->getCraftingManager()->matchBrewingRecipe($ingredient, $potion);
					if($recipe != null and !$potion->isNull()){
						$this->inventory->setItem($i, $recipe->getResult());
					}
				}

				$ingredient->count--;
				if($ingredient->getCount() <= 0) $ingredient = Item::get(Item::AIR);
				$this->inventory->setIngredient($ingredient);
			}

			$ret = true;
		}else{
			$this->namedtag->setShort(self::TAG_COOK_TIME, self::MAX_BREW_TIME);
			foreach($this->getInventory()->getViewers() as $player){
				$windowId = $player->getWindowId($this->getInventory());
				if($windowId > 0){
					$pk = new ContainerSetDataPacket();
					$pk->windowid = $windowId;
					$pk->property = 0; //Brew
					$pk->value = 0;
					$player->dataPacket($pk);
				}
			}
		}

		$this->timings->stopTiming();

		return $ret;
	}

	public function addAdditionalSpawnData(CompoundTag $nbt){
        $nbt->setShort(self::TAG_COOK_TIME, self::MAX_BREW_TIME);

        if($this->hasName()) {
            $nbt->setTag($this->namedtag->getTag("CustomName"));
        }
    }
}