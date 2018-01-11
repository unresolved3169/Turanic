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

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedSandstoneSlab extends Slab {

	protected $id = Block::RED_SANDSTONE_SLAB;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Red Sandstone Slab";
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($face === 0){
			if($blockClicked->getId() === self::RED_SANDSTONE_SLAB and ($blockClicked->getDamage() & 0x08) === 0x08){
				return $this->getLevel()->setBlock($blockClicked, BlockFactory::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);
			}elseif($blockReplace->getId() === self::RED_SANDSTONE_SLAB){
				return $this->getLevel()->setBlock($blockReplace, BlockFactory::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);
			}else{
				$this->meta |= 0x08;
			}
		}elseif($face === 1){
			if($blockClicked->getId() === self::RED_SANDSTONE_SLAB and ($blockClicked->getDamage() & 0x08) === 0){
				return $this->getLevel()->setBlock($blockClicked, BlockFactory::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);
			}elseif($blockReplace->getId() === self::RED_SANDSTONE_SLAB){
				return $this->getLevel()->setBlock($blockClicked, BlockFactory::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);
			}
			//TODO: check for collision
		}else{
			if($blockReplace->getId() === self::RED_SANDSTONE_SLAB){
				$this->getLevel()->setBlock($blockReplace, BlockFactory::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);
			}else{
				if($clickVector->y > 0.5){
					$this->meta |= 0x08;
				}
			}
		}

		if($blockReplace->getId() === self::RED_SANDSTONE_SLAB and ($blockClicked->getDamage() & 0x07) !== ($this->meta & 0x07)){
			return false;
		}
		$this->getLevel()->setBlock($blockReplace, $this, true, true);

		return true;
	}
}