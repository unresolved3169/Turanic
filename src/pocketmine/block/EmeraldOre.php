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
use pocketmine\item\Item;

class EmeraldOre extends Solid{

	protected $id = self::EMERALD_ORE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Emerald Ore";
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}

    public function getToolHarvestLevel() : int{
        return TieredTool::TIER_IRON;
    }

	public function getHardness() : float{
		return 3;
	}

	public function getDropsForCompatibleTool(Item $item): array{
        return [
            Item::get(Item::EMERALD)
        ];
    }
}
