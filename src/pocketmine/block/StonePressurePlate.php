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

use pocketmine\entity\Living;

class StonePressurePlate extends PressurePlate {
	protected $id = self::STONE_PRESSURE_PLATE;

	public function __construct(int $meta = 0){
        parent::__construct($meta);
        $this->onPitch = 0.6;
        $this->offPitch = 0.5;
    }

	public function getName() : string{
		return "Stone Pressure Plate";
	}

    protected function computeRedstoneStrength(): int{
        $bbs = $this->getCollisionBoxes();

        foreach($bbs as $bb){
            foreach($this->level->getCollidingEntities($bb) as $entity){
                if($entity instanceof Living && $entity->doesTriggerPressurePlate()){
                    return 15;
                }
            }
        }
        return 0;
    }
}