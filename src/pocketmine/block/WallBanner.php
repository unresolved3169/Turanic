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

use pocketmine\level\Level;
use pocketmine\math\Vector3;

class WallBanner extends StandingBanner{

	protected $id = self::WALL_BANNER;

	public function getName(){
		return "Wall Banner";
	}

	public function onUpdate($type){
		$faces = [
			Vector3::SIDE_NORTH => 3,
			Vector3::SIDE_SOUTH => 2,
			Vector3::SIDE_WEST => 5,
			Vector3::SIDE_EAST => 4
		];
		
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if(isset($faces[$this->meta])){
				if($this->getSide($faces[$this->meta])->getId() === self::AIR){
					$this->getLevel()->useBreakOn($this);
				}
				
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		
		return false;
	}
}