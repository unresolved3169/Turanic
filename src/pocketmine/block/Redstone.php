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

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Redstone extends Solid {

	protected $id = self::REDSTONE_BLOCK;

	public function isActivated(Block $from = null){
        return true;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        $kontrol = false;
        foreach ([self::SIDE_NORTH, self::SIDE_SOUTH, self::SIDE_WEST, self::SIDE_EAST] as $side) {
            /** @var RedstoneWire $wire */
            $wire = $this->getSide($side);
            if($wire->getId() == $this->id){
                if($wire->isActivated()){
                    $kontrol = true; // found redstone wire
                    break;
                }
            }
        }
        if(!$kontrol)
            $this->level->updateAroundRedstone($this);
        return true;
    }

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getResistance() : float{
        return 10;
    }

    public function getHardness() : float{
        return 5;
    }

    public function isRedstoneSource(){
        return true;
    }

    public function getWeakPower(int $side): int{
        return 15;
    }

    /**
	 * @return \pocketmine\math\AxisAlignedBB
	 */
	public function getBoundingBox(){
		return Block::getBoundingBox();
	}

	public function canBeFlowedInto() : bool{
		return false;
	}

	public function isSolid() : bool{
		return true;
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getName() : string{
		return "Block of Redstone";
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= 1){
			return [
				[Item::REDSTONE_BLOCK, 0, 1],
			];
		}else{
			return [];
		}
	}

    public function canHarvestWithHand(): bool{
        return false;
    }
}
