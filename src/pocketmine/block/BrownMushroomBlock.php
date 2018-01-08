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

class BrownMushroomBlock extends Solid {

	const BROWN = 14;

	protected $id = self::BROWN_MUSHROOM_BLOCK;

	public function __construct(int $meta = 14){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Brown Mushroom Block";
	}

	public function getHardness() : float{
		return 0.2;
	}

	public function getDrops(Item $item) : array{
		if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0){
			return [
				Item::get(Item::BROWN_MUSHROOM_BLOCK, self::BROWN)
			];
		}else{
			return [
				Item::get(Item::BROWN_MUSHROOM, 0, mt_rand(0, 2))
			];
		}
	}
}
