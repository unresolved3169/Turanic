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

declare(strict_types = 1);

namespace pocketmine\item;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class LingeringPotion extends ProjectileItem {

    public function __construct($meta = 0, $count = 1){
        parent::__construct(Item::LINGERING_POTION, $meta, $count, $this->getNameByMeta($meta));
    }

    public function getNameByMeta($meta){
        switch($meta){
            case Potion::WATER_BOTTLE:
                return "Lingering Water Bottle";
            case Potion::MUNDANE:
            case Potion::MUNDANE_EXTENDED:
                return "Lingering Mundane Potion";
            case Potion::THICK:
                return "Lingering Thick Potion";
            case Potion::AWKWARD:
                return "Lingering Awkward Potion";
            case Potion::INVISIBILITY:
            case Potion::INVISIBILITY_T:
                return "Lingering Potion of Invisibility";
            case Potion::LEAPING:
            case Potion::LEAPING_T:
                return "Lingering Potion of Leaping";
            case Potion::LEAPING_TWO:
                return "Lingering Potion of Leaping II";
            case Potion::FIRE_RESISTANCE:
            case Potion::FIRE_RESISTANCE_T:
                return "Lingering Potion of Fire Residence";
            case Potion::SWIFTNESS:
            case Potion::SWIFTNESS_T:
                return "Lingering Potion of Swiftness";
            case Potion::SWIFTNESS_TWO:
                return "Lingering Potion of Swiftness II";
            case Potion::SLOWNESS:
            case Potion::SLOWNESS_T:
                return "Lingering Potion of Slowness";
            case Potion::WATER_BREATHING:
            case Potion::WATER_BREATHING_T:
                return "Lingering Potion of Water Breathing";
            case Potion::HARMING:
                return "Lingering Potion of Harming";
            case Potion::HARMING_TWO:
                return "Lingering Potion of Harming II";
            case Potion::POISON:
            case Potion::POISON_T:
                return "Lingering Potion of Poison";
            case Potion::POISON_TWO:
                return "Lingering Potion of Poison II";
            case Potion::HEALING:
                return "Lingering Potion of Healing";
            case Potion::HEALING_TWO:
                return "Lingering Potion of Healing II";
            case Potion::NIGHT_VISION:
            case Potion::NIGHT_VISION_T:
                return "Lingerin Potion of Night Vision";
            default:
                return "Lingering Potion";
        }
    }

    public function getMaxStackSize(): int{
        return 16;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, CompoundTag $nbt = null) : bool{
        if($player->server->allowSplashPotion) {
            if($nbt == null){
                $nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight(), 0), $directionVector, $player->yaw, $player->pitch);
                $nbt->setShort("PotionId", $this->meta);
            }
            return parent::onClickAir($player, $directionVector, $nbt);
        }

        return true;
    }

    public function getProjectileEntityType(): string{
        return "LingeringPotion";
    }

    public function getThrowForce(): float{
        return 1.1;
    }
}