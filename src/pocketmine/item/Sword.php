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

use pocketmine\block\Block;
use pocketmine\block\BlockToolType;

class Sword extends TieredTool {

	public function isSword() : int{
		return $this->tier;
	}

    public function getBlockToolType() : int{
        return BlockToolType::TYPE_SWORD;
    }

    public function getAttackPoints() : int{
        return self::getBaseDamageFromTier($this->tier);
    }

    public function getBlockToolHarvestLevel() : int{
        return 1;
    }

    public function getMiningEfficiency(Block $block) : float{
        return parent::getMiningEfficiency($block) * 1.5; //swords break any block 1.5x faster than hand
    }

    protected function getBaseMiningEfficiency() : float{
        return 10;
    }
}