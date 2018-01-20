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

use pocketmine\entity\Animal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class Rabbit extends Animal {
	const NETWORK_ID = self::RABBIT;

	const DATA_RABBIT_TYPE = 18;
	const DATA_JUMP_TYPE = 19;

	const TYPE_BROWN = 0;
	const TYPE_WHITE = 1;
	const TYPE_BLACK = 2;
	const TYPE_BLACK_WHITE = 3;
	const TYPE_GOLD = 4;
	const TYPE_SALT_PEPPER = 5;
	const TYPE_KILLER_BUNNY = 99;

	public $height = 0.5;
	
	public $drag = 0.2;
	public $gravity = 0.3;

	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		$this->setMaxHealth(3);
		parent::initEntity();
	}

	/**
	 * Rabbit constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 */
	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag("RabbitType")){
			$nbt->setByte("RabbitType", $this->getRandomRabbitType());
		}
		parent::__construct($level, $nbt);

		$this->propertyManager->setByte(self::DATA_RABBIT_TYPE, $this->getRabbitType());
	}

	/**
	 * @return int
	 */
	public function getRandomRabbitType() : int{
		$arr = [0, 1, 2, 3, 4, 5, 99];
		return $arr[mt_rand(0, count($arr) - 1)];
	}

	/**
	 * @param int $type
	 */
	public function setRabbitType(int $type){
		$this->namedtag->setByte("RabbitType", $type);
	}

	/**
	 * @return int
	 */
	public function getRabbitType() : int{
		return (int) $this->namedtag["RabbitType"];
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Rabbit";
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		$lootingL = 0;
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::LOOTING);
			}
		}
		$drops = [ItemItem::get(ItemItem::RABBIT_HIDE, 0, mt_rand(0, 1))];
		if($this->getLastDamageCause() === EntityDamageEvent::CAUSE_FIRE){
			$drops[] = ItemItem::get(ItemItem::COOKED_RABBIT, 0, mt_rand(0, 1));
		}else{
			$drops[] = ItemItem::get(ItemItem::RAW_RABBIT, 0, mt_rand(0, 1));
		}
		if(mt_rand(1, 200) <= (5 + 2 * $lootingL)){
			$drops[] = ItemItem::get(ItemItem::RABBIT_FOOT, 0, 1);
		}
		return $drops;
	}

    public function getXpDropAmount(): int{
        return !$this->isBaby() ? mt_rand(1,3) : 0;
    }
}
