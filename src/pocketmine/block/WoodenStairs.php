<?php

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class WoodenStairs extends Stair {

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		return [
			[$this->getItemId(), $this->getVariant(), 1]
		];
	}

	/**
	 * @return int
	 */
	public function getBurnChance() : int{
		return 5;
	}

	/**
	 * @return int
	 */
	public function getBurnAbility() : int{
		return 20;
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return 2;
	}

	public function getResistance(){
        return 15;
    }
}