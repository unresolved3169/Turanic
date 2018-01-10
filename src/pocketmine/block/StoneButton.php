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
use pocketmine\level\sound\ButtonClickSound;
use pocketmine\Player;

class StoneButton extends WoodenButton {
	protected $id = self::STONE_BUTTON;

	public function getName() : string{
		return "Stone Button";
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		if(!$this->isActivated()){
			$this->meta ^= 0x08;
			$this->getLevel()->setBlock($this, $this, true, false);
			$this->getLevel()->addSound(new ButtonClickSound($this));
			$this->getLevel()->scheduleDelayedBlockUpdate($this, 40);
            $this->level->updateAroundRedstone($this);
            $this->level->updateAroundRedstone($this->getSide($this->getOpposite()));
		}
		return true;
	}
}
