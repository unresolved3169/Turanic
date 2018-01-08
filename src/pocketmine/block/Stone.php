<?php

/*
 *
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
 *
*/

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\item\Item;

class Stone extends Solid {
	const NORMAL = 0;
	const GRANITE = 1;
	const POLISHED_GRANITE = 2;
	const DIORITE = 3;
	const POLISHED_DIORITE = 4;
	const ANDESITE = 5;
	const POLISHED_ANDESITE = 6;

	protected $id = self::STONE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 1.5;
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getName() : string{
		static $names = [
			self::NORMAL => "Stone",
			self::GRANITE => "Granite",
			self::POLISHED_GRANITE => "Polished Granite",
			self::DIORITE => "Diorite",
			self::POLISHED_DIORITE => "Polished Diorite",
			self::ANDESITE => "Andesite",
			self::POLISHED_ANDESITE => "Polished Andesite"
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
			if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0 and $this->getDamage() === 0){
				return parent::getDrops($item);
			}
			return [
				Item::get($this->getDamage() === 0 ? Item::COBBLESTONE : Item::STONE, $this->getDamage())
			];
		}else{
			return [];
		}
	}

    public function canHarvestWithHand(): bool{
        return false;
    }
}