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

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Minecart extends Item {
	/**
	 * Minecart constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(self::MINECART, $meta, "Minecart");
	}

    /**
     * @return int
     */
    public function getMaxStackSize(): int{
	    return 1;
    }

	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		$minecart = Entity::createEntity("Minecart", $player->getLevel(), Entity::createBaseNBT($blockReplace->add(0,0.8,0)));
		if($minecart instanceof Entity) {
            $this->count--;
            $minecart->spawnToAll();
		}

		return true;
	}
}
