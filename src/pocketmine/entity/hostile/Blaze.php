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
use pocketmine\Player;

class Blaze extends Monster {
	const NETWORK_ID = self::BLAZE;

	public $width = 0.3;
	public $height = 0;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Blaze";
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
				$drops = [ItemItem::get(ItemItem::BLAZE_ROD, 0, mt_rand(0, 1 + $lootingL))];

				return $drops;
			}
		}
		return [];

	}

	public function getXpDropAmount(): int{
        return 10;
    }
}