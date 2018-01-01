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

class Sign extends Item {
	/**
	 * Sign constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		$this->block = Block::get(Item::SIGN_POST);
		parent::__construct(self::SIGN, 0, "Sign");
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 16;
	}
}