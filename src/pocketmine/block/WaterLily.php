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
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class WaterLily extends Flowable {

	protected $id = self::WATER_LILY;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function isSolid() : bool{
		return false;
	}

	public function getName() : string{
		return "Lily Pad";
	}

	public function getHardness() : float{
		return 0;
	}

	public function canPassThrough() : bool{
		return true;
	}

	protected function recalculateBoundingBox(){
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x,
			$this->y + 0.0625,
			$this->z
		);
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($blockClicked instanceof Water){
			$up = $blockClicked->getSide(Vector3::SIDE_UP);
			if($up->getId() === Block::AIR){
				$this->getLevel()->setBlock($up, $this, true, true);
				return true;
			}
		}

		return false;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if(!($this->getSide(0) instanceof Water)){
				$this->getLevel()->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

}