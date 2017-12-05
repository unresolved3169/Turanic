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

use pocketmine\item\Tool;
use pocketmine\level\Level;

class RedstoneLamp extends Solid {
	protected $id = self::REDSTONE_LAMP;

	/**
	 * RedstoneLamp constructor.
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
		return 0;
	}

	public function getResistance(){
        return 1.5;
    }

    public function getHardness(){
        return 0.3;
    }

    public function getToolType(){
        return Tool::TYPE_PICKAXE;
    }

    /**
	 * @return string
	 */
	public function getName() : string{
		return "Redstone Lamp";
	}

    public function onUpdate($type){
        if($type == Level::BLOCK_UPDATE_NORMAL || $type == Level::BLOCK_UPDATE_REDSTONE){
            if($this->level->isBlockPowered($this)){
                $this->level->setBlock($this, new LitRedstoneLamp(), false, false);
            }
        }
	}
}
