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

use pocketmine\item\Item;

class Leaves2 extends Leaves {

	protected $id = self::LEAVES2;
    protected $woodType = self::WOOD2;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		static $names = [
			self::ACACIA => "Acacia Leaves",
			self::DARK_OAK => "Dark Oak Leaves",
		];
		return $names[$this->getVariant()];
	}

    public function getSaplingItem() : Item{
        return Item::get(Item::SAPLING, $this->getVariant() + 4);
    }

    public function canDropApples() : bool{
        return $this->meta === self::DARK_OAK;
    }
}
