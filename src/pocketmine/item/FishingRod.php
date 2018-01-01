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

use pocketmine\event\player\PlayerUseFishingRodEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class FishingRod extends ProjectileItem {

	/**
	 * FishingRod constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(self::FISHING_ROD, 0, "Fishing Rod");
	}

    public function getProjectileEntityType() : string{
        return "FishingHook";
    }

    public function getThrowForce() : float{
        return 0.6;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, CompoundTag $nbt = null): bool{
        $player->server->getPluginManager()->callEvent($ev = new PlayerUseFishingRodEvent($player, ($player->isFishing() ? PlayerUseFishingRodEvent::ACTION_STOP_FISHING : PlayerUseFishingRodEvent::ACTION_START_FISHING)));
        if(!$ev->isCancelled()){
            if(!$player->isFishing()){
                return parent::onClickAir($player, $directionVector, $nbt);
            }
        }
        return true;
    }
} 
