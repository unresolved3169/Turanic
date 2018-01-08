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

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\tile\DLDetector;
use pocketmine\tile\Tile;

class DaylightDetector extends Transparent {

	protected $id = self::DAYLIGHT_DETECTOR;

	public function __construct(int $meta = 0){
        $this->meta = 0;
	}

	public function getName() : string{
		return "Daylight Sensor";
	}

	public function getBoundingBox(){
		if($this->boundingBox === null){
			$this->boundingBox = $this->recalculateBoundingBox();
		}
		return $this->boundingBox;
	}

	public function canBeFlowedInto(){
		return false;
	}

	protected function getTile(){
		$t = $this->getLevel()->getTile($this);
		if($t instanceof DLDetector){
			return $t;
		}else{
		    /** @var DLDetector $t */
			$t = Tile::createTile(Tile::DL_DETECTOR, $this->getLevel(), DLDetector::createNBT($this));
            return $t;
		}
	}

	public function onActivate(Item $item, Player $player = null){
		$this->getLevel()->setBlock($this, new DaylightDetectorInverted(), true, true);
		$this->getTile()->onUpdate();
		return true;
	}

	public function isActivated(Block $from = null){
		return $this->getTile()->isActivated();
	}

	public function getHardness() : float{
		return 0.2;
	}

	public function getDrops(Item $item) : array{
		return [
			Item::get(self::DAYLIGHT_SENSOR)
		];
	}

	public function getFuelTime(): int{
        return 300;
    }

    public function isRedstoneSource(){
        return true;
    }

    public function getWeakPower(int $side): int{
        return $this->getTile()->getLightByTime();
    }
}