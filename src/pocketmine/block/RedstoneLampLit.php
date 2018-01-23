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

namespace pocketmine\block;

use pocketmine\block\utils\RedstoneUtils;
use pocketmine\level\Level;

class RedstoneLampLit extends RedstoneLamp implements SolidLight{

	protected $id = self::LIT_REDSTONE_LAMP;
	/** @var bool */
	private $delayed = false;

	public function getName() : string{
		return "Lit Redstone Lamp";
	}

	public function getLightLevel() : int{
		return 15;
	}

	public function onUpdate(int $type){
	    switch($type){
            case Level::BLOCK_UPDATE_NORMAL:
            case Level::BLOCK_UPDATE_REDSTONE:
                if (!RedstoneUtils::isRedstonePowered($this))
                    $this->delayed = true;
                    $this->level->scheduleDelayedBlockUpdate($this, 4);
                break;
            case Level::BLOCK_UPDATE_SCHEDULED:
                if($this->delayed or !RedstoneUtils::isRedstonePowered($this))
                    $this->level->setBlock($this, Block::get(Block::REDSTONE_LAMP), false, false);
                break;
        }
	}
}
