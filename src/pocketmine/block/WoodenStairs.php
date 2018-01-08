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