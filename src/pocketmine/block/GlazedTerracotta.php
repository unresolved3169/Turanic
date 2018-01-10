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

use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class GlazedTerracotta extends Solid{

    public function getHardness() : float{
        return 1.4;
    }

    public function getToolType() : int{
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel() : int{
        return TieredTool::TIER_WOODEN;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        if($player !== null){
            $faces = [
                0 => 4,
                1 => 3,
                2 => 5,
                3 => 2
            ];
            $this->meta = $faces[(~($player->getDirection() - 1)) & 0x03];
        }
        return $this->getLevel()->setBlock($blockReplace, $this, true, true);
    }

    public function getVariantBitmask() : int{
        return 0;
    }
}