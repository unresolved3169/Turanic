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
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DragonEgg extends Fallable {
	protected $id = self::DRAGON_EGG;

    const RAND_VERTICAL = [-7, -6, -5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7];
	const RAND_HORIZONTAL = [-15, -14, -13, -12, -11, -10, -9, -8, -7, -6, -5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Dragon Egg";
	}

	public function getHardness() : float{
		return 3;
	}

	public function getBlastResistance() : float{
		return 45;
	}

	public function getLightLevel() : int{
        return 1;
    }

	public function isBreakable(Item $item) : bool{
		return false;
	}

    public function onActivate(Item $item, Player $player = null) : bool{
        while(true){
            $level = $this->getLevel();
            $x = $this->getX() + self::RAND_HORIZONTAL[array_rand(self::RAND_HORIZONTAL)];
            $y = $this->getY() + self::RAND_VERTICAL[array_rand(self::RAND_VERTICAL)];
            $z = $this->getZ() + self::RAND_HORIZONTAL[array_rand(self::RAND_HORIZONTAL)];
            if($y < Level::Y_MAX && $level->getBlockIdAt($x, $y, $z) == 0){
                break;
            }
        }
        /** @noinspection PhpUndefinedVariableInspection */
        $level->setBlock($this, new Air(), true, true);
        $oldpos = clone $this;
        /** @noinspection PhpUndefinedVariableInspection */
        /** @noinspection PhpUndefinedVariableInspection */
        /** @noinspection PhpUndefinedVariableInspection */
        $pos = new Position($x, $y, $z, $level);
        $newpos = clone $pos;
        $level->setBlock($pos, $this, true, true);
        $posdistance = $newpos->subtract($oldpos->x, $oldpos->y, $oldpos->z);
        $intdistance = $oldpos->distance($newpos);

        for($c = 0; $c <= $intdistance; $c++){
            $progress = $c / $intdistance;
            $this->getLevel()->broadcastLevelEvent(new Vector3($oldpos->x + $posdistance->x * $progress, 1.62 + $oldpos->y + $posdistance->y * $progress, $oldpos->z + $posdistance->z * $progress), 2010);
        }

        return true;
    }
}
