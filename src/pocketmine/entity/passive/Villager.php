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

namespace pocketmine\entity\passive;

use pocketmine\entity\Ageable;
use pocketmine\entity\Creature;
use pocketmine\entity\NPC;
use pocketmine\inventory\VillagerTradeInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;

class Villager extends Creature implements NPC, Ageable, InventoryHolder {
	
	const NETWORK_ID = self::VILLAGER;

    const PROFESSION_FARMER = 0;
    const PROFESSION_LIBRARIAN = 1;
    const PROFESSION_PRIEST = 2;
    const PROFESSION_BLACKSMITH = 3;
    const PROFESSION_BUTCHER = 4;

	public $width = 0.6;
	public $height = 1.8;

    public function initEntity(){
		parent::initEntity();

        /** @var int $profession */
        $profession = $this->namedtag->getInt("Profession", self::PROFESSION_FARMER);

        if($profession > 4 or $profession < 0){
            $profession = self::PROFESSION_FARMER;
        }

        $this->setProfession($profession);

        // Example
        $this->addTradeItem(0, 7, Item::get(Item::DIAMOND, 0, 5), Item::get(Item::DIAMOND_BLOCK));
        $this->addTradeItems(0, 7, Item::get(Item::DIAMOND, 0, 5), Item::get(Item::BOOK), Item::get(Item::BEACON));
	}

	public function saveNBT(){
        parent::saveNBT();
        $this->namedtag->setInt("Profession", $this->getProfession());
    }

	public function getName() : string{
	    static $names = [
	        self::PROFESSION_FARMER => "Farmer",
	        self::PROFESSION_LIBRARIAN => "Librarian",
	        self::PROFESSION_PRIEST => "Priest",
	        self::PROFESSION_BLACKSMITH => "Blacksmith",
	        self::PROFESSION_BUTCHER => "Butcher"
        ];
		return $names[$this->getProfession()];
	}

    /**
     * Sets the villager profession
     *
     * @param int $profession
     */
    public function setProfession(int $profession){
        $this->propertyManager->setInt(self::DATA_VARIANT, $profession);
    }

    public function getProfession() : int{
        return $this->propertyManager->getInt(self::DATA_VARIANT);
    }

    public function isBaby() : bool{
        return $this->getGenericFlag(self::DATA_FLAG_BABY);
    }

    public function getXpDropAmount(): int{
        return 0;
    }

    public function onInteract(Player $player, Item $item){
        $player->addWindow($this->getInventory());
        return true;
    }

    public function addTradeItem(int $rewardExp, int $maxUses, Item $buyA, Item $sell){
        $offers = $this->getOffers();
        $tradeItem = new CompoundTag("", [
            $buyA->nbtSerialize(-1, "buyA"),
            new IntTag("maxUses", $maxUses),
            new ByteTag("rewardExp", $rewardExp),
            $sell->nbtSerialize(-1, "sell"),
            new IntTag("uses", 0),
        ]);
        $recipes = $offers->getListTag("Recipes");
        $recipes->offsetSet(count($recipes), $tradeItem);
        $offers->setTag($recipes);
        $this->namedtag->setTag($offers);
    }

    public function addTradeItems(int $rewardExp, int $maxUses, Item $buyA, Item $buyB, Item $sell){
        $offers = $this->getOffers();
        $tradeItem = new CompoundTag("", [
            $buyA->nbtSerialize(-1, "buyA"),
            $buyB->nbtSerialize(-1, "buyB"),
            new IntTag("maxUses", $maxUses),
            new ByteTag("rewardExp", $rewardExp),
            $sell->nbtSerialize(-1, "sell"),
            new IntTag("uses", 0),
        ]);
        $recipes = $offers->getListTag("Recipes");
        $recipes->offsetSet(count($recipes), $tradeItem);
        $offers->setTag($recipes);
        $this->namedtag->setTag($offers);
    }

    public function getOffers() : CompoundTag{
        if($this->namedtag->hasTag("Offers")){
            return $this->namedtag->getCompoundTag("Offers");
        }else{
            $offers = new CompoundTag("Offers", [new ListTag("Recipes")]);
            $this->namedtag->setTag($offers);
            return $offers;
        }
    }

    public function getInventory() : VillagerTradeInventory{
        return new VillagerTradeInventory($this);
    }
}
