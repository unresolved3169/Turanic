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

namespace pocketmine\entity\neutral;

use pocketmine\entity\Entity;
use pocketmine\entity\Monster;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\Player;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class ZombiePigman extends Monster {
	const NETWORK_ID = self::ZOMBIE_PIGMAN;

	public $width = 0.6;
	public $height = 0;

	public $drag = 0.2;
	public $gravity = 0.3;
	
	public function initEntity(){
			$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
			$this->addBehavior(new StrollBehavior($this));
			$this->addBehavior(new LookAtPlayerBehavior($this));
			$this->addBehavior(new RandomLookaroundBehavior($this));
			//2 Armor Points
			$this->setMaxHealth(20);
			$this->propertyManager->setInt(Entity::DATA_VARIANT, 10);
			parent::initEntity();
		}
		
	/**
	 * @return string
	 */
	public function getName() : string{
		return "Zombie Pigman";
	}
	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		parent::spawnTo($player);

		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->item = new ItemItem(283);
        $pk->inventorySlot = $pk->hotbarSlot = 0;
		$player->dataPacket($pk);
	}

    /**
     * @return array|ItemItem[]
     * @throws \TypeError
     */
    public function getDrops(){
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::LOOTING);
				if(mt_rand(1, 200) <= (5 + 2 * $lootingL)){
					$drops[] = ItemItem::get(ItemItem::GOLD_INGOT, 0, 1);
				}
				$drops[] = ItemItem::get(ItemItem::GOLD_NUGGET, 0, mt_rand(0, 1 + $lootingL));
				$drops[] = ItemItem::get(ItemItem::ROTTEN_FLESH, 0, mt_rand(0, 1 + $lootingL));
				return $drops;
			}
		}
		return [];
	}

    /**
     * @return bool
     */
    public function isBaby(){
        return $this->getGenericFlag(self::DATA_FLAG_BABY);
    }

    public function getXpDropAmount(): int{
        return !$this->isBaby() ? 5 : 12;
    }
}
