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

use pocketmine\entity\Living;

abstract class Food extends Item implements FoodSource {


	public function requiresHunger() : bool{
		return true;
	}

	/**
	 * @return Item
	 */
	public function getResidue(){
        return Item::get(Item::AIR, 0, 0);
	}

	public function getAdditionalEffects() : array{
		return [];
	}

	public function onConsume(Living $consumer){
	}
}
