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

namespace pocketmine\block;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;

class Gravel extends Fallable {

	protected $id = self::GRAVEL;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Gravel";
	}

	public function getHardness() : float{
		return 0.6;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_SHOVEL;
	}

	public function getDrops(Item $item) : array{
		$drops = [];
		if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0){//使用精准采集附魔 不掉落燧石
			$drops[] = Item::get(Item::GRAVEL);
			return $drops;
		}
		$fortunel = $item->getEnchantmentLevel(Enchantment::TYPE_MINING_FORTUNE);
		$fortunel = $fortunel > 3 ? 3 : $fortunel;
		$rates = [10, 7, 4, 1];
		if(mt_rand(1, $rates[$fortunel]) === 1){//10% 14% 25% 100%
			$drops[] = Item::get(Item::FLINT);
		}
		if(mt_rand(1, 10) !== 1){//90%
			$drops[] = Item::get(Item::GRAVEL);
		}
		return $drops;
	}
}
