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

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\Player;

class RedstoneTorch extends Flowable {

	protected $id = self::REDSTONE_TORCH;

	/**
	 * RedstoneTorch constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return int
	 */
	public function getLightLevel(){
		return 7;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Redstone Torch";
	}

	/**
	 * @param Item $item
	 *
	 * @return mixed|void
	 */
	public function onBreak(Item $item){
		$this->getLevel()->setBlock($this, new Air(), true, true);
		$faces = [
			1 => 4,
			2 => 5,
			3 => 2,
			4 => 3,
			5 => 0,
			6 => 0,
			0 => 0,
		];
        foreach($faces as $face){
            $this->level->updateAround($this->getSide($face));
        }
	}

	/**
	 * @param Item        $item
	 * @param Block       $block
	 * @param Block       $target
	 * @param int         $face
	 * @param float       $fx
	 * @param float       $fy
	 * @param float       $fz
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$below = $this->getSide(0);
        $faces = [
            1 => 5,
            2 => 4,
            3 => 3,
            4 => 2,
            5 => 1,
        ];

        if(!$target->isTransparent() and $face !== 0){
			$this->meta = $faces[$face];
			$this->getLevel()->setBlock($block, $this, true, true);

			foreach($faces as $face){
                $this->level->updateAround($this->getSide($face));
            }
			return true;
		}elseif(!$below->isTransparent() or $below->getId() === self::FENCE or $below->getId() === self::COBBLE_WALL){
			$this->meta = 0;
			$this->getLevel()->setBlock($block, $this, true, true);

            foreach($faces as $face){
                $this->level->updateAround($this->getSide($face));
            }
			return true;
		}

		return false;
	}

	public function getWeakPower(int $side): int{
        return 15;
    }

    public function isRedstoneSource(){
        return true;
    }

    /**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		return [
			[Item::LIT_REDSTONE_TORCH, 0, 1],
		];
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return true;
	}
}
