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

class RandomLookaroundBehavior extends Behavior{

    public $entity;
    public $duration = 0;
    public $rotation = 0;

    public function getName() : string{
        return "RandomLookaround";
    }

    public function shouldStart() : bool{
        $shouldStart = rand(0,50) == 0;
        if(!$shouldStart) return false;

        $this->duration = 20 + rand(0,20);
        $this->rotation = rand(-180,180);

        return true;
    }

    public function canContinue() : bool{
        return $this->duration-- > 0 and abs($this->rotation) > 0;
    }

    public function onTick(){
        $this->entity->yaw += $this->signRot($this->rotation) * 10;
        $this->rotation -= 10;
    }

    public function signRot(int $val){
        if($val > 0) return 1;

        if($val < 0) return -1;

        return 0;
    }

    public function onEnd(){
    }
}