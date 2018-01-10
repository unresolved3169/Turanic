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

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

abstract class Fallable extends Solid {

	public function onUpdate(int $type){
        if($type === Level::BLOCK_UPDATE_NORMAL){
            $down = $this->getSide(Vector3::SIDE_DOWN);
            if($down->getId() === self::AIR or $down instanceof Liquid or $down instanceof Fire){
                $this->level->setBlock($this, Block::get(Block::AIR), true);

                $nbt = Entity::createBaseNBT($this->add(0.5, 0, 0.5));
                $nbt->setInt("TileID", $this->getId());
                $nbt->setByte("Data", $this->getDamage());

                $fall = Entity::createEntity("FallingSand", $this->getLevel(), $nbt);

                if($fall !== null){
                    $fall->spawnToAll();
                }
            }
        }
	}

    public function tickFalling(){
        return null;
    }
}