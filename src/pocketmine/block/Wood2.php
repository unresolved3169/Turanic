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

class Wood2 extends Wood {

	const ACACIA = 0;
	const DARK_OAK = 1;

	protected $id = self::WOOD2;

	/**
	 * @return string
	 */
	public function getName() : string{
		static $names = [
			0 => "Acacia Wood",
			1 => "Dark Oak Wood"
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}
}
