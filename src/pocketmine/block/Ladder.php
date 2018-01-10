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

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Ladder extends Transparent {

	protected $id = self::LADDER;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Ladder";
	}

	public function hasEntityCollision() : bool{
		return true;
	}

	public function isSolid() : bool{
		return false;
	}

	public function getHardness() : float{
		return 0.4;
	}

	public function onEntityCollide(Entity $entity){
		$entity->resetFallDistance();
		$entity->onGround = true;
	}

	protected function recalculateBoundingBox(){

		$f = 0.125;

		if($this->meta === 2){
			return new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z + 1 - $f,
				$this->x + 1,
				$this->y + 1,
				$this->z + 1
			);
		}elseif($this->meta === 3){
			return new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z,
				$this->x + 1,
				$this->y + 1,
				$this->z + $f
			);
		}elseif($this->meta === 4){
			return new AxisAlignedBB(
				$this->x + 1 - $f,
				$this->y,
				$this->z,
				$this->x + 1,
				$this->y + 1,
				$this->z + 1
			);
		}elseif($this->meta === 5){
			return new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z,
				$this->x + $f,
				$this->y + 1,
				$this->z + 1
			);
		}

		return null;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($blockClicked->isTransparent() === false){
			$faces = [
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5,
			];
			if(isset($faces[$face])){
				$this->meta = $faces[$face];
				return $this->getLevel()->setBlock($blockReplace, $this, true, true);
			}
		}

		return false;
	}

	public function onUpdate(int $type){
		$faces = [
			2 => 3,
			3 => 2,
			4 => 5,
			5 => 4,
		];
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if(isset($faces[$this->meta])){
				if($this->getSide($faces[$this->meta])->getId() === self::AIR){
					$this->getLevel()->useBreakOn($this);
				}
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		return false;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}
}
