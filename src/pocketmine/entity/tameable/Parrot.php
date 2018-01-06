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

namespace pocketmine\entity\tameable;

use pocketmine\entity\FlyingAnimal;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;

class Parrot extends FlyingAnimal {

    const NETWORK_ID = self::PARROT;

    const COLOR_RED = 1;
    const COLOR_BLUE = 2;
    const COLOR_CYAN = 3;
    const COLOR_SILVER = 4;

    public $width = 0.5;
    public $height = 0.9;

    public $drag = 0.2;
    public $gravity = 0.3;

    public function __construct(Level $level, CompoundTag $nbt){
        if(!isset($nbt->Variant)){
            $nbt->Variant = new IntTag("Variant", mt_rand(0, 4));
        }
        parent::__construct($level, $nbt);
    }

    /**
     * @param int $type
     */
    public function setColor(int $type){
        $this->namedtag->setInt("Variant", $type);
    }

    /**
     * @return int
     */
    public function getColor() : int{
        return $this->namedtag->getInt("Variant", mt_rand(0,4));
    }

    /**
     * @return mixed
     */
    public function getName(){
        return "Parrot";
    }

    public function initEntity(){
        $this->setHealth(6);
        parent::initEntity();
    }

    public function getDrops(){
        $drops = [
            Item::get(Item::FEATHER, 0, mt_rand(1, 2))
        ];

        return $drops;
    }

    public function getXpDropAmount(): int{
        return !$this->isBaby() ? mt_rand(1,3) : 0;
    }
}