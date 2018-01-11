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

class Bed extends Item {

	const WHITE_BED = 0;
	const ORANGE_BED = 1;
	const MAGENTA_BED = 2;
	const LIGHT_BLUE_BED = 3;
	const YELLOW_BED = 4;
	const LIME_BED = 5;
	const PINK_BED = 6;
	const GRAY_BED = 7;
	const LIGHT_GRAY_BED = 8;
	const CYAN_BED = 9;
	const PURPLE_BED = 10;
	const BLUE_BED = 11;
	const BROWN_BED = 12;
	const GREEN_BED = 13;
	const RED_BED = 14;
	const BLACK_BED = 15;

    /**
     * Bed constructor.
     *
     * @param int $meta
     */
	public function __construct(int $meta = self::WHITE_BED){
		$this->block = BlockFactory::get(Item::BED_BLOCK, $meta);
		parent::__construct(self::BED, $meta, "Bed"); //TODO: Bed Names
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 1;
	}
}
