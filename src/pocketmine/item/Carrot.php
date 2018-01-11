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
use pocketmine\block\BlockFactory;

class Carrot extends Food {
	/**
	 * Carrot constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		$this->block = BlockFactory::get(Item::CARROT_BLOCK);
		parent::__construct(self::CARROT, 0, "Carrot");
	}

	/**
	 * @return int
	 */
	public function getFoodRestore() : int{
		return 3;
	}

	/**
	 * @return float
	 */
	public function getSaturationRestore() : float{
		return 4.8;
	}
}
