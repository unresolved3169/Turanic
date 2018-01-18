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
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Boat extends Item {
	/**
	 * Boat constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::BOAT, $meta, "Boat");
	}

	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		$realPos = $blockReplace->getSide($face);

		$nbt = Entity::createBaseNBT($realPos->add(0.5, 0, 0.5));
		$nbt->setInt("WoodID", $this->getDamage());

		$boat = Entity::createEntity("Boat", $player->getLevel(), $nbt);
		if($boat != null) $boat->spawnToAll();

        $this->count--;

		return true;
	}

	public function getFuelTime(): int{
        return 1200; // 400 in PC
    }
}
