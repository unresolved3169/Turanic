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

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\utils\Color;
use pocketmine\utils\MainLogger;

abstract class Armor extends Item {
	const TIER_LEATHER = 1;
	const TIER_GOLD = 2;
	const TIER_CHAIN = 3;
	const TIER_IRON = 4;
	const TIER_DIAMOND = 5;

	const TYPE_HELMET = 0;
	const TYPE_CHESTPLATE = 1;
	const TYPE_LEGGINGS = 2;
	const TYPE_BOOTS = 3;

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 1;
	}

	/**
	 * @return bool
	 */
	public function isArmor(){
		return true;
	}

	/**
	 *
	 * @param Item $object
	 * @param int  $cost
	 *
	 * @return bool
	 */
	public function useOn($object, int $cost = 1){
		if($this->isUnbreakable()){
			return true;
		}
		$unbreakings = [
			0 => 100,
			1 => 80,
			2 => 73,
			3 => 70
		];
		$unbreakingl = $this->getEnchantmentLevel(Enchantment::TYPE_MINING_DURABILITY);
		if(mt_rand(1, 100) > $unbreakings[$unbreakingl]){
			return true;
		}
		$this->setDamage($this->getDamage() + $cost);
		if($this->getDamage() >= $this->getMaxDurability()){
			$this->setCount(0);
		}
		return true;
	}

	/**
	 * @return bool
	 */
	public function isUnbreakable(){
		$tag = $this->getNamedTagEntry("Unbreakable");
		return $tag !== null and $tag->getValue() > 0;
	}

	/**
	 * @param Color $color
	 */
	public function setCustomColor(Color $color){
		if(($hasTag = $this->hasCompoundTag())){
			$tag = $this->getNamedTag();
		}else{
			$tag = new CompoundTag("", []);
		}
		$tag->customColor = new IntTag("customColor", $color->toRGB());
		$this->setCompoundTag($tag);
	}

	/**
	 * @return mixed|null
	 */
	public function getCustomColor(){
		if(!$this->hasCompoundTag()) return null;
		$tag = $this->getNamedTag();
		if(isset($tag->customColor)){
			return $tag["customColor"];
		}
		return null;
	}

	public function clearCustomColor(){
		if(!$this->hasCompoundTag()) return;
		$tag = $this->getNamedTag();
		if(isset($tag->customColor)){
			unset($tag->customColor);
		}
		$this->setCompoundTag($tag);
	}

	/**
	 * @return bool
	 */
	public function getArmorTier(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function getArmorType(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function getMaxDurability(){
		return false;
	}

    /**
     * @return int
     */
    public function getDefensePoints() : int{
        return 0;
    }

	/**
	 * @return bool
	 */
	public function isHelmet(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isChestplate(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isLeggings(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isBoots(){
		return false;
	}

	public function onClickAir(Player $player, Vector3 $directionVector): bool{
	    $item = Item::get(Block::AIR);
        switch($this->getArmorType()){
            case Armor::TYPE_HELMET:
                $old = $player->getInventory()->getHelmet();
                if($old->getId() != Item::AIR){
                    $item = $old;
                }
                $player->getInventory()->setHelmet($this);
                break;
            case Armor::TYPE_CHESTPLATE:
                $old = $player->getInventory()->getChestplate();
                if($old->getId() != Item::AIR){
                    $item = $old;
                }
                $player->getInventory()->setChestplate($this);
                break;
            case Armor::TYPE_LEGGINGS:
                $old = $player->getInventory()->getLeggings();
                if($old->getId() != Item::AIR){
                    $item = $old;
                }
                $player->getInventory()->setLeggings($this);
                break;
            case Armor::TYPE_BOOTS:
                $old = $player->getInventory()->getBoots();
                if($old->getId() != Item::AIR){
                    $item = $old;
                }
                $player->getInventory()->setBoots($this);
                break;
            default:
                MainLogger::getLogger()->debug("Zırh tespit edilemedi. (ID: ".$this->getId().")");
                return false;
        }
        $player->getInventory()->setItemInHand($item);
        return false; // because not set item air
    }
}