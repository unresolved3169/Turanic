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

class Carrot extends Crops {

	protected $id = self::CARROT_BLOCK;
	protected $itemId = Item::CARROT;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Carrot Block";
	}

	public function getDropsForCompatibleTool(Item $item) : array{
        return [
            Item::get(Item::CARROT, 0, $this->meta >= 0x07 ? mt_rand(1, 4) : 1)
        ];
	}

    public function getPickedItem(): Item{
        return Item::get(Item::CARROT);
    }

    public function ticksRandomly(): bool{
        return true;
    }
}
