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

namespace pocketmine\item;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\Player;

class SplashPotion extends ProjectileItem {

	/**
	 * SplashPotion constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::SPLASH_POTION, $meta, $count, $this->getNameByMeta($meta));
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 1;
	}

	/**
	 * @param int $meta
	 *
	 * @return string
	 */
	public function getNameByMeta(int $meta){
		return "Splash " . Potion::getNameByMeta($meta);
	}

    public function getProjectileEntityType() : string{
        return "ThrownPotion";
    }

    public function getThrowForce() : float{
        return 1.1;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, CompoundTag $nbt = null): bool{
        if($player->server->allowSplashPotion) {
            $nbt = new CompoundTag("", [
                new ListTag("Pos", [
                    new DoubleTag("", $player->x),
                    new DoubleTag("", $player->y + $player->getEyeHeight()),
                    new DoubleTag("", $player->z)
                ]),
                new ListTag("Motion", [
                    new DoubleTag("", $directionVector->x),
                    new DoubleTag("", $directionVector->y),
                    new DoubleTag("", $directionVector->z)
                ]),
                new ListTag("Rotation", [
                    new FloatTag("", $player->yaw),
                    new FloatTag("", $player->pitch)
                ]),
                new ShortTag("PotionId", $this->getDamage())
            ]);
            return parent::onClickAir($player, $directionVector, $nbt);
        }
        return true;
    }
}