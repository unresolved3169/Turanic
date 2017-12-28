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

/**
 *  Interface implemented by objects that can be consumed by players, giving them food and saturation.
 */
interface FoodSource extends Consumable {

	/**
	 * @return int
	 */
	public function getFoodRestore() : int;

	/**
	 * @return float
	 */
	public function getSaturationRestore() : float;

    /**
     * Returns whether a Human eating this FoodSource must have a non-full hunger bar.
     * @return bool
     */
	public function requiresHunger() : bool;

}
