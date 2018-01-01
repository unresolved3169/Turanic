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

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class EnderPearl extends ProjectileItem {

	/**
	 * EnderPearl constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(Item::ENDER_PEARL, $meta, "Ender Pearl");
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 16;
	}

    public function getProjectileEntityType() : string{
        return "EnderPearl";
    }

    public function getThrowForce() : float{
        return 1.1;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, CompoundTag $nbt = null): bool{
        if(floor(($time = microtime(true)) - $player->lastEnderPearlUse) >= 1){
            if(parent::onClickAir($player, $directionVector, $nbt)) $player->lastEnderPearlUse = $time;
        }
        return true;
    }

}