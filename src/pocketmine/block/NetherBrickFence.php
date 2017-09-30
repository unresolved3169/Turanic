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
use pocketmine\utils\Color;

class NetherBrickFence extends Transparent {

	protected $id = self::NETHER_BRICK_FENCE;

	/**
	 * NetherBrickFence constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return 2;
	}

	public function getResistance(){
  return 10;
 }

 /**
	 * @return int
	 */
	public function getToolType(){
		//Different then the woodfences
		return Tool::TYPE_PICKAXE;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Nether Brick Fence";
	}

	/**
	 * @param Block $block
	 *
	 * @return bool
	 */
	public function canConnect(Block $block){
		return ($block instanceof NetherBrickFence || $block instanceof FenceGate) or ($block->isSolid() and !$block->isTransparent());
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[Item::NETHER_BRICK_FENCE, $this->meta, 1],
			];
		}else{
			return [];
		}
	}

	public function canHarvestWithHand(): bool{
  return false;
	}

	public function getColor(){
	 return new Color(112, 2, 0);
 }
}