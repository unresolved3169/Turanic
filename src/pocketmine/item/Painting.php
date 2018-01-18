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

class Painting extends Item {

	public function __construct(int $meta = 0){
		parent::__construct(self::PAINTING, 0, "Painting");
	}

	/**
	 * @return bool
	 */
	public function canBeActivated() : bool{
		return true;
	}

	public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
		if($blockClicked->isTransparent() === false and $face > 1 and $blockReplace->isSolid() === false){
			$faces = [
				2 => 1,
				3 => 3,
				4 => 0,
				5 => 2,
			];
			$motives = [
				// Motive Width Height
				["Kebab", 1, 1],
				["Aztec", 1, 1],
				["Alban", 1, 1],
				["Aztec2", 1, 1],
				["Bomb", 1, 1],
				["Plant", 1, 1],
				["Wasteland", 1, 1],
				["Wanderer", 1, 2],
				["Graham", 1, 2],
				["Pool", 2, 1],
				["Courbet", 2, 1],
				["Sunset", 2, 1],
				["Sea", 2, 1],
				["Creebet", 2, 1],
				["Match", 2, 2],
				["Bust", 2, 2],
				["Stage", 2, 2],
				["Void", 2, 2],
				["SkullAndRoses", 2, 2],
				["Wither", 2, 2],
				["Fighters", 4, 2],
				["Skeleton", 4, 3],
				["DonkeyKong", 4, 3],
				["Pointer", 4, 4],
				["Pigscene", 4, 4],
				["Flaming Skull", 4, 4],
			];

			$right = [4, 5, 3, 2];

			$validMotives = [];
			foreach($motives as $motive){
				$valid = true;
				for($x = 0; $x < $motive[1] && $valid; $x++){
					for($z = 0; $z < $motive[2] && $valid; $z++){
						if($blockClicked->getSide($right[$face - 2], $x)->isTransparent() ||
							$blockClicked->getSide(Vector3::SIDE_UP, $z)->isTransparent() ||
                            $blockReplace->getSide($right[$face - 2], $x)->isSolid() ||
                            $blockReplace->getSide(Vector3::SIDE_UP, $z)->isSolid()
						){
							$valid = false;
						}
					}
				}

				if($valid){
					$validMotives[] = $motive;
				}
			}

			$motive = $validMotives[array_rand($validMotives)];

            $nbt = Entity::createBaseNBT($blockClicked, null, $faces[$face] * 90);
            $nbt->setString("Motive", $motive[0]);

			$painting = Entity::createEntity("Painting", $player->getLevel(), $nbt);
			if($painting != null) $painting->spawnToAll();

            $this->count--;

			return true;
		}

		return false;
	}

}