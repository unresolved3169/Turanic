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

class SpawnEgg extends Item {
	/**
	 * SpawnEgg constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::SPAWN_EGG, $meta, $count, "Spawn Egg");
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		if($blockReplace->getId() == Block::MONSTER_SPAWNER){
			return true;
		}else{
            $nbt = Entity::createBaseNBT($blockReplace->add(0.5, 0, 0.5), null, lcg_value() * 360, 0);

			if($this->hasCustomName()){
				$nbt->setString("CustomName", $this->getCustomName());
			}

			$entity = Entity::createEntity($this->meta, $level, $nbt);

			if($entity instanceof Entity){
				if($player->isSurvival()){
					--$this->count;
				}
				$entity->spawnToAll();

				return true;
			}

			return false;
		}
	}
}