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

namespace pocketmine\entity\behavior;

use pocketmine\entity\Mob;
use pocketmine\block\Air;
use pocketmine\math\Vector3;

class StrollBehavior extends Behavior{

    public $duration;
    public $timeLeft;
    public $speed;
    public $speedMultiplier;

    public function __construct(Mob $entity, int $duration = 60, float $speed = 0.25, float $speedMultiplier = 0.75){
        parent::__construct($entity);

        $this->duration = $duration;
        $this->speed = $speed;
        $this->speedMultiplier = $speedMultiplier;
        $this->timeLeft = $duration;
    }

    public function getName() : string{
        return "Stroll";
    }

    public function shouldStart() : bool{
        return rand(0,50) == 0; //$this->entity->random->nextRange(0, 120) == 0;
    }

    public function canContinue() : bool{
        return $this->timeLeft-- > 0;
    }

    public function onTick(){
        $speedFactor = $this->speed * $this->speedMultiplier;

        $level = $this->entity->level;

        $direction = $this->entity->getDirectionVector();
        $direction->y *= 0;

        $random = $this->entity->random;

        $entity = $this->entity;

        $blockDown = $level->getBlock($entity->add(0,-1,0));

        if($this->entity->motionY < 0 and $blockDown instanceof Air){
            $this->timeLeft = 0;
            return;
        }

        $motion = $direction->multiply($speedFactor);
        $coord = $this->getDirectionCoord();

        $block = $level->getBlock($coord);
        $blockUp = $level->getBlock($coord->add(0,1,0));

        if($block->isSolid()/*and !$blockUp->isSolid()*/){
            $motion->y += 0.7;
        }elseif($block->isSolid() and $blockUp->isSolid()){
            $this->timeLeft = 0;
            return;
        }


        $entities = $level->getNearbyEntities($entity->getBoundingBox()->grow(1,1,1), $entity);
        $entityCollide = count($entities) > 0;

        if($entityCollide){
            $rot = rand(0,2) == 0 ? rand(45,180) : rand(-180,-45);

            $this->entity->yaw += $rot;
            $this->entity->setMotion(new Vector3(0,0,0));
        }else{
            $vm = $this->entity->getMotion();
            $vm->y *= 0;
            if($vm->length() < $motion->length()){
                $this->entity->setMotion($vm->add($motion->subtract($vm)));
            }else{
                $this->entity->setMotion($motion);
            }
        }
    }

    public function getDirectionCoord(){
        $d = $this->entity->getDirection();
        $vec = $this->entity;
        if($d == Mob::NORTH) $vec->add(0,0,-1);
        if($d == Mob::SOUTH) $vec->add(0,0,1);
        if($d == Mob::WEST) $vec->add(-1,0,0);
        if($d == Mob::EAST) $vec->add(1,0,0);

        return $vec;
    }

    public function onEnd(){
        $this->timeLeft = $this->duration;
    }
}