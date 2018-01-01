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

use pocketmine\entity\Effect;

class RawChicken extends Food {
	/**
	 * RawChicken constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(self::RAW_CHICKEN, $meta, "Raw Chicken");
	}

	/**
	 * @return int
	 */
	public function getFoodRestore() : int{
		return 2;
	}

	/**
	 * @return float
	 */
	public function getSaturationRestore() : float{
		return 1.2;
	}

	/**
	 * @return array
	 */
	public function getAdditionalEffects() : array{
		$chance = mt_rand(0, 100);
		if($chance >= 70){
			return [Effect::getEffect(Effect::HUNGER)->setDuration(30 * 20)];
		}else{
			return [];
		}
	}
}

