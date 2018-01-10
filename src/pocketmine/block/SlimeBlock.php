<?php

/*
 *
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
 *
*/

declare(strict_types=1);

namespace pocketmine\block;

class SlimeBlock extends Solid {

	protected $id = self::SLIME_BLOCK;

	public function __construct(int $meta = 15){
		$this->meta = $meta;
	}

	public function hasEntityCollision() : bool{
		return true;
	}

	public function getHardness() : float{
		return 0;
	}

	public function getName() : string{
		return "Slime Block";
	}
}
