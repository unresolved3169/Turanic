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
use pocketmine\math\Vector3;
use pocketmine\Player;

class Redstone extends Solid {

	protected $id = self::REDSTONE_BLOCK;

    public function __construct(int $meta = 0){
        $this->meta = $meta;
    }

    public function getName() : string{
        return "Block of Redstone";
    }

    public function getHardness() : float{
        return 5;
    }

    public function getBlastResistance() : float{
        return 30;
    }

    public function canBeFlowedInto() : bool{
		return false;
	}

    public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}

    public function getToolHarvestLevel(): int{
        return TieredTool::TIER_WOODEN;
    }

    public function isRedstoneSource() : bool{
        return true;
    }

    public function getRedstonePower(): int{
        return 15;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player); // TODO: REDSTONE SYSTEM
    }
}
