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

namespace pocketmine\block;

class IronTrapdoor extends Trapdoor {
	protected $id = self::IRON_TRAPDOOR;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Iron Trapdoor";
	}

	public function getHardness() : float{
		return 5;
	}

	public function getResistance() : float{
		return 25;
	}

	public function canHarvestWithHand(): bool{
        return false;
    }
}