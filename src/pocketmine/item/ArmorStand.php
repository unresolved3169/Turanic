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

class ArmorStand extends Item {

    /**
     * ArmorStand constructor.
     *
     * @param int $meta
     * @param int $count
     */
    public function __construct($meta = 0, $count = 1){
        parent::__construct(self::ARMOR_STAND, $meta, "Armor Stand");
    }

    public function canBeActivated(): bool{
        return true;
    }

    public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
        $as = Entity::createEntity("ArmorStand", $level, Entity::createBaseNBT($blockReplace->add(0.5, 0, 0.5), null, $this->getDirection($player->yaw)));

        if ($as != null) {
            if ($player->isSurvival()) {
                $this->count--;
            }
            $as->spawnToAll();
            return true;
        }
        return false;
    }

    public function getDirection($yaw) : float {
        $rotation = $yaw % 360;
        if($rotation < 0){
            $rotation += 360.0;
        }
        if ((0 <= $rotation && $rotation < 22.5) || (337.5 <= $rotation && $rotation < 360)) {
            return 180;
		} else if (22.5 <= $rotation && $rotation < 67.5) {
            return 225;
		} else if (67.5 <= $rotation && $rotation < 112.5) {
            return 270;
		} else if (112.5 <= $rotation && $rotation < 157.5) {
            return 315;
		} else if (157.5 <= $rotation && $rotation < 202.5) {
            return 0;
		} else if (202.5 <= $rotation && $rotation < 247.5) {
            return 45;
		} else if (247.5 <= $rotation && $rotation < 292.5) {
            return 90;
		} else if (292.5 <= $rotation && $rotation < 337.5) {
            return 135;
		} else {
            return 0;
		}
    }
}