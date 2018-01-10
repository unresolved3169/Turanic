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

class RedstoneLamp extends Solid {
	protected $id = self::REDSTONE_LAMP;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel() : int{
		return 0;
	}

    public function getHardness() : float{
        return 0.3;
    }

    public function getToolType() : int{
        return BlockToolType::TYPE_PICKAXE;
    }

	public function getName() : string{
		return "Redstone Lamp";
	}

    public function onUpdate(int $type){
        if($type == Level::BLOCK_UPDATE_NORMAL || $type == Level::BLOCK_UPDATE_REDSTONE){
            if($this->level->isBlockPowered($this) or $this->level->isBlockPowered($this->getSide(self::SIDE_UP))){
                $this->level->setBlock($this, new LitRedstoneLamp(), false, false);
            }
        }
	}
}
