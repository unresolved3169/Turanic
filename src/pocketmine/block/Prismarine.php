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

use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\item\Tool;

class Prismarine extends Solid {

	const NORMAL = 0;
	const DARK = 1;
	const BRICKS = 2;

	protected $id = self::PRISMARINE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 1.5;
	}

	public function getName() : string{
		static $names = [
			self::NORMAL => "Prismarine",
			self::DARK => "Dark Prismarine",
			self::BRICKS => "Prismarine Bricks",
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
			return [
				parent::getDrops($item)
			];
		}else{
			return [];
		}
	}

    public function getVariantBitmask() : int{
        return 0x03;
    }

    public function canHarvestWithHand(): bool{
        return false;
    }
}