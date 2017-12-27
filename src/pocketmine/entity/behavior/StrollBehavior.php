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
        return rand(0,120) == 0;
    }

    public function canContinue() : bool{
        return $this->timeLeft-- > 0;
    }

    public function onTick(){
        $speedFactor = (float) ($this->speed*$this->speedMultiplier*0.7*($this->entity->isInsideOfWater() ? 0.3 : 1.0)); // 0.7 is a general mob base factor
		$level = $this->entity->getLevel();
		$coordinates = $this->entity->getPosition();
		$direction = $this->entity->getDirectionVector();
		$direction->y = 0;
		$entity = $this->entity;

		$blockDown = $level->getBlock($coordinates->add(0,-1,0));
		if ($entity->getMotion()->y < 0 && $blockDown instanceof Air)
		{
			$this->timeLeft = 0;
			return;
		}

	    $coord = ($coordinates->add($direction->multiply($speedFactor))->add($direction->multiply(0.5)));

		$players = $entity->getViewers();

		$block = $level->getBlock($coord);
		$blockUp = $level->getBlock($coord->add(0,1,0));
		$blockUpUp = $level->getBlock($coord->add(0,2,0));

		$colliding = $block->isSolid() or ($entity->getHeight() >= 1 and $blockUp->isSolid());
		if (!$colliding)
		{
			$motion = $direction->multiply($speedFactor);
			$pm = $entity->getMotion();
			$pm->y = 0;
			if ($pm->length() < $motion->length())
			{
				$entity->setMotion($pm->add($motion->x - $pm->x, 0, $motion->z - $pm->z));
			}
			else
			{
				$entity->setMotion($motion);
			}
		}
		else
		{
			if (!$blockUp->isSolid() and !($entity->getHeight() > 1 and $blockUpUp->isSolid()) and rand(0,4) != 0)
			{
				$entity->motionY = 0.42;
			}
			else
			{
				//TODO
			}
		}
    }

    public function onEnd(){
        $this->timeLeft = $this->duration;
        $this->entity->setMotion(new Vector3(0,0,0));
    }
}