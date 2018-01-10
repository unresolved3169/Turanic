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

class WoodenStairs extends Stair {

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}

	public function getBurnChance() : int{
		return 5;
	}

	public function getBurnAbility() : int{
		return 20;
	}

	public function getHardness() : float{
		return 2;
	}

	public function getBlastResistance() : float{
        return 15;
    }
}