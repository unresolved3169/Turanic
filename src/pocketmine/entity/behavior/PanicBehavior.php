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

class PanicBehavior extends StrollBehavior{

    public function __construct(Mob $entity, $speed = 0.25, $speedMultiplier = 0.75){
        parent::__construct($entity, 60, $speed, $speedMultiplier);
    }

    public function shouldStart() : bool{
        return $this->entity->getLastDamageCause() != null;
    }
    
    public function onEnd(){
    	parent::onEnd();
    	$this->entity->resetLastDamageCause();
    }

}