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

class DoubleRedSandstoneSlab extends DoubleSlab {

	protected $id = Block::DOUBLE_RED_SANDSTONE_SLAB;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Double Red Sandstone Slab";
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= 1){
			return [
				[Item::RED_SANDSTONE_SLAB, $this->meta, 2],
			];
		}else{
			return [];
		}
	}

 /**
  * @return bool
  */
 public function canHarvestWithHand(): bool{
  return false;
 }
}