<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

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