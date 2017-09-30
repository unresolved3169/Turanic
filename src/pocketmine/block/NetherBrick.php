<?php

/*
 *
 *
 * _______  _
 *   |__   __|   (_)
 *   | |_   _ _ __ __ _ _ __  _  ___
 *   | | | | | '__/ _` | '_ \| |/ __|
 *   | | |_| | | | (_| | | | | | (__
 *   |_|\__,_|_|  \__,_|_| |_|_|\___|
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

use pocketmine\item\Item;
use pocketmine\item\Tool;

class NetherBrick extends Solid {

	protected $id = self::NETHER_BRICKS;

	/**
	 * NetherBrick constructor.
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
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
		return "Nether Bricks";
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return 2;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= 1){
			return [
				[Item::NETHER_BRICKS, 0, 1],
			];
		}else{
			return [];
		}
	}

	public function canHarvestWithHand(): bool{
  return false;
 }
}
