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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\TieredTool;
use pocketmine\item\Item;

class NetherBrickFence extends Transparent {

	protected $id = self::NETHER_BRICK_FENCE;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 2;
	}

	public function getToolType() : int{
		//Different then the woodfences
		return BlockToolType::TYPE_PICKAXE;
	}

	public function getName() : string{
		return "Nether Brick Fence";
	}

	public function canConnect(Block $block){
		return ($block instanceof NetherBrickFence || $block instanceof FenceGate) or ($block->isSolid() and !$block->isTransparent());
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= TieredTool::TIER_WOODEN){
			return parent::getDrops($item);
		}else{
			return [];
		}
	}
}