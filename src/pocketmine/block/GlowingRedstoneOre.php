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
use pocketmine\level\Level;

class GlowingRedstoneOre extends RedstoneOre implements SolidLight {

	protected $id = self::GLOWING_REDSTONE_ORE;

	public function getName() : string{
		return "Glowing Redstone Ore";
	}

	public function getLightLevel() : int{
		return 9;
	}

	public function onUpdate(int $type){
		if($type === Level::BLOCK_UPDATE_SCHEDULED or $type === Level::BLOCK_UPDATE_RANDOM){
			$this->getLevel()->setBlock($this, BlockFactory::get(Item::REDSTONE_ORE, $this->meta), false, false);

			return Level::BLOCK_UPDATE_WEAK;
		}

		return false;
	}
}