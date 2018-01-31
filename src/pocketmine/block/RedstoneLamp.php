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

use pocketmine\block\utils\RedstoneUtils;
use pocketmine\level\Level;

class RedstoneLamp extends Solid {
	protected $id = self::REDSTONE_LAMP;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

    public function getName() : string{
        return "Redstone Lamp";
    }

    public function getHardness() : float{
        return 0.3;
    }

    public function onUpdate(int $type){
        if($type == Level::BLOCK_UPDATE_NORMAL || $type == Level::BLOCK_UPDATE_REDSTONE){
            if(RedstoneUtils::isRedstonePowered($this->asPosition())){
                $this->level->setBlock($this, Block::get(Block::LIT_REDSTONE_LAMP));
            }
        }
	}

	public function canUpdateWithRedstone(): bool{
        return true;
    }
}
