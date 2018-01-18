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
use pocketmine\entity\projectile\FireworksRocket;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\Random;

class FireworkRocket extends Item {

    public $spread = 5.0;

    public function __construct(int $meta = 0){
        parent::__construct(self::FIREWORK, $meta, "Firework Rocket");
    }

    public function getMaxStackSize(): int{
        return 16;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, CompoundTag $nbt = null) : bool{
        // elytra booster
        if($player->isUseElytra()){
            $this->count--;
            $motion = $player->getDirectionVector()->multiply(1.25);
            $nbt = Entity::createBaseNBT($player->asVector3(), $motion , mt_rand(0, 360), -1*(float) (90.0 + (5.0 - 5.0/2)));
            /** @var CompoundTag $tags */
            $tags = $this->getNamedTagEntry("Fireworks");
            if (!is_null($tags)){
                $nbt->setTag($tags);
            }

            $level = $player->getLevel();
            $rocket = new FireworksRocket($level, $nbt, $player, $this);
            $level->addEntity($rocket);
            if ($rocket instanceof Entity){
                --$this->count;
                $rocket->spawnToAll();
                return true;
            }
        }
        return true;
    }

    public function onActivate(Level $level, Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickPos) : bool{
        $random = new Random();
        $yaw = $random->nextBoundedInt(360);
        $pitch = -1 * (float)(90 + ($random->nextFloat() * $this->spread - $this->spread / 2));
        $nbt = Entity::createBaseNBT($blockReplace->add(0.5, 0, 0.5), null, $yaw, $pitch);

        /** @var CompoundTag $tags */
        $tags = $this->getNamedTagEntry("Fireworks");
        if (!is_null($tags)){
            $nbt->setTag($tags);
        }

        $rocket = new FireworksRocket($level, $nbt, $player, $this, $random);
        $level->addEntity($rocket);
        if ($rocket instanceof Entity){
            --$this->count;
            $rocket->spawnToAll();
            return true;
        }

        return false;
    }
}