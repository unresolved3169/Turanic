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
use pocketmine\item\Tool;
use pocketmine\level\sound\DoorSound;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Trapdoor extends Transparent {

	protected $id = self::TRAPDOOR;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Wooden Trapdoor";
	}

	public function getHardness() : float{
		return 3;
	}

	protected function recalculateBoundingBox(){
		$damage = $this->getDamage();

		$f = 0.1875;

		if(($damage & 0x08) > 0){
			$bb = new AxisAlignedBB(
				$this->x,
				$this->y + 1 - $f,
				$this->z,
				$this->x + 1,
				$this->y + 1,
				$this->z + 1
			);
		}else{
			$bb = new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z,
				$this->x + 1,
				$this->y + $f,
				$this->z + 1
			);
		}

		if(($damage & 0x04) > 0){
			if(($damage & 0x03) === 0){
				$bb->setBounds(
					$this->x,
					$this->y,
					$this->z + 1 - $f,
					$this->x + 1,
					$this->y + 1,
					$this->z + 1
				);
			}elseif(($damage & 0x03) === 1){
				$bb->setBounds(
					$this->x,
					$this->y,
					$this->z,
					$this->x + 1,
					$this->y + 1,
					$this->z + $f
				);
			}
			if(($damage & 0x03) === 2){
				$bb->setBounds(
					$this->x + 1 - $f,
					$this->y,
					$this->z,
					$this->x + 1,
					$this->y + 1,
					$this->z + 1
				);
			}
			if(($damage & 0x03) === 3){
				$bb->setBounds(
					$this->x,
					$this->y,
					$this->z,
					$this->x + $f,
					$this->y + 1,
					$this->z + 1
				);
			}
		}

		return $bb;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$directions = [
			0 => 1,
			1 => 3,
			2 => 0,
			3 => 2
		];
		if($player !== null){
			$this->meta = $directions[$player->getDirection() & 0x03];
		}
		if(($clickVector->y > 0.5 and $face !== self::SIDE_UP) or $face === self::SIDE_DOWN){
			$this->meta |= 0b00000100; //top half of block
		}
		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		return true;
	}

	/**
	 * @return bool
	 */
	public function isOpened(){
		return (($this->meta & 0b00001000) === 0);
	}

	/**
	 * @param Item        $item
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = \null){
		$this->meta ^= 0b00001000;
		$this->getLevel()->setBlock($this, $this, true);
		$this->level->addSound(new DoorSound($this));
		return true;
	}

	/**
	 * @return int
	 */
	public function getToolType() : int{
		return Tool::TYPE_AXE;
	}

	public function getFuelTime(): int{
        return 300;
    }
}
