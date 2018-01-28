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

namespace pocketmine\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\item\Potion;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class LingeringPotion extends Projectile {

    const NETWORK_ID = self::LINGERING_POTION;

    const DATA_POTION_ID = 16;

    public $width = 0.25;
    public $height = 0.25;

    protected $gravity = 0.1;
    protected $drag = 0.05;

    public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
        if(!isset($nbt->PotionId)){
            $nbt->setShort("PotionId", Potion::AWKWARD);
        }
        parent::__construct($level, $nbt, $shootingEntity);
        $this->propertyManager->removeProperty(self::DATA_SHOOTER_ID);
        $this->propertyManager->setShort(self::DATA_VARIANT, $this->getPotionId());
        $this->propertyManager->setShort(self::DATA_POTION_ID, $this->getPotionId());
        $this->setGenericFlag(self::DATA_FLAG_LINGER, true);
    }

    public function getPotionId() : int{
        return $this->namedtag->getShort("PotionId", Potion::AWKWARD);
    }

    public function onUpdate(int $currentTick){
        if($this->closed){
            return false;
        }

        if($this->age < 2){
            $aec = null;
            $potionId = $this->getPotionId();
            $color = Potion::getColor($potionId)->toArray();

            $nbt = Entity::createBaseNBT($this);
            $nbt->setFloat("Radius", 3);
            $nbt->setFloat("RadiusOnUse", -0.5);
            $nbt->setFloat("RadiusPerTick", -0.005);
            $nbt->setInt("Age", 0);
            $nbt->setInt("WaitTime", 10);
            $nbt->setInt("Duration", 600);
            $nbt->setInt("DurationOnUse", 0);
            $nbt->setInt("Age", 0);
            $nbt->setIntArray("Color", $color);
            $nbt->setShort("PotionId", $potionId);

            $aec = Entity::createEntity("AreaEffectCloud", $this->getLevel(), $nbt);
            if($aec instanceof Entity){
                $aec->spawnToAll();
            }
        }else{
            $this->close();
        }

        return parent::onUpdate($currentTick);
    }

    public function onCollideWithPlayer(Player $player): bool{
        return false;
    }

}