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

namespace pocketmine\item;

use pocketmine\block\BlockToolType;

class Shears extends Tool {

	public function __construct(int $meta = 0){
		parent::__construct(self::SHEARS, $meta, "Shears");
	}

    public function getMaxDurability() : int{
        return 239;
    }

    public function isShears() : bool{
        return true;
    }

    public function getBlockToolType() : int{
        return BlockToolType::TYPE_SHEARS;
    }

    public function getBlockToolHarvestLevel() : int{
        return 1;
    }

    protected function getBaseMiningEfficiency() : float{
        return 15;
    }
}