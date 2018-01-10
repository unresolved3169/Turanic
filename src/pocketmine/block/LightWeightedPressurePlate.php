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

use pocketmine\item\TieredTool;
use pocketmine\math\Math;

class LightWeightedPressurePlate extends PressurePlate {
	protected $id = self::LIGHT_WEIGHTED_PRESSURE_PLATE;

    public function __construct(int $meta = 0){
        parent::__construct($meta);
        $this->onPitch = 0.90000004;
        $this->offPitch = 0.75;
    }

	public function getName() : string{
		return "Light Weighted Pressure Plate";
	}

    protected function computeRedstoneStrength(): int{
        $bbs = $this->getCollisionBoxes();

        foreach($bbs as $bb){
            $count = min(count($this->level->getCollidingEntities($bb)), $this->getMaxWeight());

            if($count > 0){
                $f = min($this->getMaxWeight(), $count) / $this->getMaxWeight();
                return Math::ceilFloat($f * 15.0);
            }else{
                return 0;
            }
        }
        return 0;
    }

    public function getMaxWeight() : int{
        return 15;
    }

    public function isSolid() : bool{
        return false;
    }

    public function getVariantBitmask() : int{
        return 0;
    }

    public function getToolType() : int{
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel() : int{
        return TieredTool::TIER_WOODEN;
    }
}