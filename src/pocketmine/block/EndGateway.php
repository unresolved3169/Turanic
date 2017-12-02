<?php

/*
 *
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
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\utils\Color;

class EndGateway extends Transparent {

    protected $id = self::END_GATEWAY;

    public function __construct($meta = 0){
        $this->meta = $meta;
    }

    public function getName(){
        return "End Gateway";
    }

    public function canPassThrough(){
        return true;
    }

    public function isBreakable(Item $item){
        return false;
    }

    public function getHardness(){
        return -1;
    }

    public function getResistance(){
        return 18000000;
    }

    public function getLightLevel(){
        return 15;
    }

    public function hasEntityCollision(){
        return true;
    }

    public function getColor(){
        return Color::getRGB(0, 0, 0);
    }
}