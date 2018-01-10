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

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\inventory\BigCraftingGrid;

//TODO: check orientation
class Workbench extends Solid {

	protected $id = self::WORKBENCH;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 2.5;
	}

	public function getName() : string{
		return "Crafting Table";
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){
			if($player->getServer()->limitedCreative and $player->isCreative()) return true;
			$player->setCraftingGrid(new BigCraftingGrid($player));
		}

		return true;
	}

	public function getFuelTime(): int{
        return 300;
    }
}