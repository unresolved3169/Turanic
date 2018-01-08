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

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;

class Sponge extends Solid {

    const SPONGE_NORMAL = 0;
    const SPONGE_WET = 1;

	protected $id = self::SPONGE;
	protected $absorbRange = 6;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 0.6;
	}

	public function absorbWater(){
		if(Server::getInstance()->absorbWater){
			$range = $this->absorbRange / 2;
			for($xx = -$range; $xx <= $range; $xx++){
				for($yy = -$range; $yy <= $range; $yy++){
					for($zz = -$range; $zz <= $range; $zz++){
						$block = $this->getLevel()->getBlock(new Vector3($this->x + $xx, $this->y + $yy, $this->z + $zz));
						if($block->getId() === Block::WATER) $this->getLevel()->setBlock($block, Block::get(Block::AIR), true, true);
						if($block->getId() === Block::STILL_WATER) $this->getLevel()->setBlock($block, Block::get(Block::AIR), true, true);
					}
				}
			}
		}
	}

	public function onUpdate($type){
		if($this->meta == 0){
			if($type === Level::BLOCK_UPDATE_NORMAL){
				$blockAbove = $this->getSide(Vector3::SIDE_UP)->getId();
				$blockBeneath = $this->getSide(Vector3::SIDE_DOWN)->getId();
				$blockNorth = $this->getSide(Vector3::SIDE_NORTH)->getId();
				$blockSouth = $this->getSide(Vector3::SIDE_SOUTH)->getId();
				$blockEast = $this->getSide(Vector3::SIDE_EAST)->getId();
				$blockWest = $this->getSide(Vector3::SIDE_WEST)->getId();

				if($blockAbove === Block::WATER ||
					$blockBeneath === Block::WATER ||
					$blockNorth === Block::WATER ||
					$blockSouth === Block::WATER ||
					$blockEast === Block::WATER ||
					$blockWest === Block::WATER
				){
					$this->absorbWater();
					$this->getLevel()->setBlock($this, Block::get(Block::SPONGE, 1), true, true);
					return Level::BLOCK_UPDATE_NORMAL;
				}
				if($blockAbove === Block::STILL_WATER ||
					$blockBeneath === Block::STILL_WATER ||
					$blockNorth === Block::STILL_WATER ||
					$blockSouth === Block::STILL_WATER ||
					$blockEast === Block::STILL_WATER ||
					$blockWest === Block::STILL_WATER
				){
					$this->absorbWater();
					$this->getLevel()->setBlock($this, Block::get(Block::SPONGE, 1), true, true);
					return Level::BLOCK_UPDATE_NORMAL;
				}
			}
			return false;
		}
		return true;
	}

	public function getName() : string{
		static $names = [
			self::SPONGE_NORMAL => "Sponge",
			self::SPONGE_WET => "Wet Sponge",
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}


	public function getVariantBitmask(): int{
        return 0x0f;
    }
}
