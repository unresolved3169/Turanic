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

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\level\sound\LaunchSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

abstract class ProjectileItem extends Item{

	abstract public function getProjectileEntityType() : string;

	abstract public function getThrowForce() : float;

	public function onClickAir(Player $player, Vector3 $directionVector, CompoundTag $nbt = null) : bool{
		if($nbt == null)
            $nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight(), 0), $directionVector, $player->yaw, $player->pitch);

		$projectile = Entity::createEntity($this->getProjectileEntityType(), $player->getLevel(), $nbt, $player);
        if($projectile !== null){
            $projectile->setMotion($projectile->getMotion()->multiply($this->getThrowForce()));
        }

        $this->count--;

		if($projectile instanceof Projectile){
			$player->getServer()->getPluginManager()->callEvent($projectileEv = new ProjectileLaunchEvent($projectile));
			if($projectileEv->isCancelled()){
                $projectile->kill();
                return false;
			}else{
			    if($this->getProjectileEntityType() == "FishingHook") $player->setFishingHook($projectile);
                $projectile->spawnToAll();
				$player->getLevel()->addSound(new LaunchSound($player), $player->getViewers());
			}
		}else{
            $projectile->spawnToAll();
            return false;
		}

		return true;
	}
}