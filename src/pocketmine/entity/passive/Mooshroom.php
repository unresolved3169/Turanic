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
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\Player;

class Mooshroom extends Animal {
	const NETWORK_ID = self::MOOSHROOM;

	public $width = 0.3;
	public $height = 1.8;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Mooshroom";
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		$lootingL = 0;
		/** @var EntityDamageByEntityEvent $cause */
		$cause = $this->lastDamageCause;
		$damager = $cause->getDamager();
		if($cause instanceof EntityDamageByEntityEvent and $damager instanceof Player){
			$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::LOOTING);
		}
		$drops = [ItemItem::get(ItemItem::RAW_BEEF, 0, mt_rand(1, 3 + $lootingL))];
		$drops[] = ItemItem::get(ItemItem::LEATHER, 0, mt_rand(0, 2 + $lootingL));
		return $drops;
	}

	public function getXpDropAmount(): int{
        return !$this->isBaby() ? mt_rand(1,3) : 0;
    }
}
