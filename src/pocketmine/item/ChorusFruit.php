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

use pocketmine\block\Liquid;
use pocketmine\entity\Living;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector3;

class ChorusFruit extends Food {
	/**
	 * ChorusFruit constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::CHORUS_FRUIT, 0, $count, "Chorus Fruit");
	}

	/**
	 * @return int
	 */
	public function getFoodRestore() : int{
		return 4;
	}

	/**
	 * @return float
	 */
	public function getSaturationRestore() : float{
		return 2.4;
	}

	public function requiresHunger(): bool{
        return false;
    }

    public function onConsume(Living $consumer){
        $minX = $consumer->getFloorX() - 8;
        $minY = $consumer->getFloorY() - 8;
        $minZ = $consumer->getFloorZ() - 8;

        $maxX = $minX + 16;
        $maxY = $minY + 16;
        $maxZ = $minZ + 16;

        $level = $consumer->getLevel();
        assert($level !== null);

        for($attempts = 0; $attempts < 16; ++$attempts){
            $x = mt_rand($minX, $maxX);
            $y = mt_rand($minY, $maxY);
            $z = mt_rand($minZ, $maxZ);

            while($y >= 0 and !$level->getBlockAt($x, $y, $z)->isSolid()){
                $y--;
            }
            if($y < 0){
                continue;
            }

            $blockUp = $level->getBlockAt($x, $y + 1, $z);
            $blockUp2 = $level->getBlockAt($x, $y + 2, $z);
            if($blockUp->isSolid() or $blockUp instanceof Liquid or $blockUp2->isSolid() or $blockUp2 instanceof Liquid){
                continue;
            }

            //Sounds are broadcasted at both source and destination
            $level->addSound(new EndermanTeleportSound($consumer->asVector3()));
            $consumer->teleport(new Vector3($x + 0.5, $y + 1, $z + 0.5));
            $level->addSound(new EndermanTeleportSound($consumer->asVector3()));

            break;
        }
    }

}