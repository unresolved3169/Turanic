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

class DaylightDetectorInverted extends DaylightDetector {
	protected $id = self::DAYLIGHT_SENSOR_INVERTED;

	public function onActivate(Item $item, Player $player = null) : bool{
		$this->getLevel()->setBlock($this, new DaylightDetector(), true, true);
		$this->getTile()->onUpdate();
		return true;
	}

	public function isRedstoneSource(){
        return false;
    }

    public function getWeakPower(int $side): int{
        return 0;
    }
}