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

class Purpur extends Quartz {

	protected $id = self::PURPUR;

	public function getHardness() : float{
		return 1.5;
	}

	public function getBlastResistance() : float{
        return 30;
    }

    public function getName() : string{
        static $names = [
            self::NORMAL => "Purpur Block",
            self::CHISELED => "Chiseled Purpur", //wtf?
            self::PILLAR => "Purpur Pillar"
        ];

        return $names[$this->getVariant()] ?? "Unknown";
    }

    public function getDrops(Item $item) : array{
        if($this->isCompatibleWithTool($item)){
            return Block::getDrops($item);
        }else{
            return [];
        }
    }

}