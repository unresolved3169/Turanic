<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\block;

use pocketmine\math\Math;

class HeavyWeightedPressurePlate extends PressurePlate {
	protected $id = self::HEAVY_WEIGHTED_PRESSURE_PLATE;

    public function __construct($meta = 0){
        parent::__construct($meta);
        $this->onPitch = 0.90000004;
        $this->offPitch = 0.75;
    }

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Heavy Weighted Pressure Plate";
	}

    protected function computeRedstoneStrength(): int{
        $bbs = $this->getCollisionBoxes();

        foreach($bbs as $bb){
            $count = min(count($this->level->getCollidingEntities($bb)), $this->getMaxWeight());

            if($count > 0){
                $f = min($this->getMaxWeight(), $count) / $this->getMaxWeight();
                return max(1, Math::ceilFloat($f * 15.0));
            }else{
                return 0;
            }
        }
        return 0;
    }

    public function getMaxWeight() : int{
        return 150;
	}
}