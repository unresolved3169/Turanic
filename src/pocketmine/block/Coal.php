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

use pocketmine\item\Item;
use pocketmine\item\Tool;

class Coal extends Solid {

	protected $id = self::COAL_BLOCK;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 5;
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getBurnChance() : int{
		return 5;
	}

	public function getBurnAbility() : int{
		return 5;
	}

	public function getName() : string{
		return "Coal Block";
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= 1){
			return [
				Item::get(Item::COAL_BLOCK)
			];
		}else{
			return [];
		}
	}

    public function canHarvestWithHand() : bool{
        return false;
    }

    public function getFuelTime() : int{
        return 16000;
    }
}