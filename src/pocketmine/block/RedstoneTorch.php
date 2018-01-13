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
use pocketmine\math\Vector3;
use pocketmine\Player;

// TODO : UPDATE
class RedstoneTorch extends Flowable {

	protected $id = self::REDSTONE_TORCH;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel() : int{
		return 7;
	}

	public function getName() : string{
		return "Redstone Torch";
	}

	public function onBreak(Item $item, Player $player = null) : bool{
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
        return parent::onBreak($item, $player);
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$below = $this->getSide(0);
        $faces = [
            1 => 5,
            2 => 4,
            3 => 3,
            4 => 2,
            5 => 1,
        ];

        if(!$blockClicked->isTransparent() and $face !== 0){
			$this->meta = $faces[$face];
			$this->getLevel()->setBlock($blockReplace, $this, true, true);

			foreach($faces as $face){
                $this->level->updateAround($this->getSide($face));
            }
			return true;
		}elseif(!$below->isTransparent() or $below->getId() === self::FENCE or $below->getId() === self::COBBLE_WALL){
			$this->meta = 0;
			$this->getLevel()->setBlock($blockReplace, $this, true, true);

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

	public function getDropsForCompatibleTool(Item $item) : array{
		return [
			Item::get(Item::REDSTONE_TORCH)
		];
	}

	public function isActivated(Block $from = null){
		return true;
	}
}
