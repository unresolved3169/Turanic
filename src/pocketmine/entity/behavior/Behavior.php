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

abstract class Behavior{

    /** @var Mob */
    public $entity;

    public function __construct(Mob $entity){
        $this->entity = $entity;
    }

    public abstract function getName() : string;

    public abstract function shouldStart() : bool;

    public abstract function onTick();

    public abstract function onEnd();

    public abstract function canContinue() : bool;
}