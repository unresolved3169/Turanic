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

namespace pocketmine\block;


use pocketmine\item\Tool;
use pocketmine\item\Item;

class StainedTerracotta extends Solid {

	protected $id = self::STAINED_TERRACOTTA;

	const CLAY_WHITE = 0;
	const CLAY_ORANGE = 1;
	const CLAY_MAGENTA = 2;
	const CLAY_LIGHT_BLUE = 3;
	const CLAY_YELLOW = 4;
	const CLAY_LIME = 5;
	const CLAY_PINK = 6;
	const CLAY_GRAY = 7;
	const CLAY_LIGHT_GRAY = 8;
	const CLAY_CYAN = 9;
	const CLAY_PURPLE = 10;
	const CLAY_BLUE = 11;
	const CLAY_BROWN = 12;
	const CLAY_GREEN = 13;
	const CLAY_RED = 14;
	const CLAY_BLACK = 15;

	/**
	 * StainedTerracotta constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 1.25;
	}

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		static $names = [
			0 => "White Stained Clay",
			1 => "Orange Stained Clay",
			2 => "Magenta Stained Clay",
			3 => "Light Blue Stained Clay",
			4 => "Yellow Stained Clay",
			5 => "Lime Stained Clay",
			6 => "Pink Stained Clay",
			7 => "Gray Stained Clay",
			8 => "Light Gray Stained Clay",
			9 => "Cyan Stained Clay",
			10 => "Purple Stained Clay",
			11 => "Blue Stained Clay",
			12 => "Brown Stained Clay",
			13 => "Green Stained Clay",
			14 => "Red Stained Clay",
			15 => "Black Stained Clay",
		];
		return $names[$this->meta & 0x0f];
	}

    /**
     * @param Item $item
     * @return array
     */
    public function getDrops(Item $item): array{
        if($item->isPickaxe() >= 1){
            return [
                [Item::HARDENED_CLAY, 0, 1],
            ];
        }
        return [];
    }
}