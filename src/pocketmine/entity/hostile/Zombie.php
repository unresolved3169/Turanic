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

namespace pocketmine\entity\hostile;

use pocketmine\entity\Monster;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\entity\behavior\{
    PanicBehavior, StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior
};

class Zombie extends Monster {
	const NETWORK_ID = self::ZOMBIE;

	public $width = 0.6;
	public $height = 0;

	public $dropExp = [5, 5];

	public $drag = 0.2;
	public $gravity = 0.3;
	
	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
        $this->setMaxHealth(20);
		parent::initEntity();
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Zombie";
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
        $pk->entityRuntimeId = $this->getId();
		$pk->type = Zombie::NETWORK_ID;
        $pk->position = $this->getPosition();
        $pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

    /**
     * @return array|ItemItem[]
     * @throws \TypeError
     */
    public function getDrops(){
		$cause = $this->lastDamageCause;
		$drops = [];
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::TYPE_WEAPON_LOOTING);
				if(mt_rand(0, 199) < (5 + 2 * $lootingL)){
					switch(mt_rand(0, 3)){
						case 0:
							$drops[] = ItemItem::get(ItemItem::IRON_INGOT, 0, 1);
							break;
						case 1:
							$drops[] = ItemItem::get(ItemItem::CARROT, 0, 1);
							break;
						case 2:
							$drops[] = ItemItem::get(ItemItem::POTATO, 0, 1);
							break;
					}
				}
				$count = mt_rand(0, 2 + $lootingL);
				if($count > 0){
					$drops[] = ItemItem::get(ItemItem::ROTTEN_FLESH, 0, $count);
				}
			}
		}

		return $drops;
	}
}