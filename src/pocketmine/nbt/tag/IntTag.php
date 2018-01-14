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

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use pocketmine\nbt\NBTStream;

#include <rules/NBT.h>

class IntTag extends NamedTag{

    /**
     * @param string $name
     * @param int    $value
     */
    public function __construct(string $name = "", int $value = 0){
        parent::__construct($name, $value);
    }

    public function getType() : int{
        return NBT::TAG_Int;
    }

    public function read(NBTStream $nbt){
        $this->value = $nbt->getInt();
    }

    public function write(NBTStream $nbt){
        $nbt->putInt($this->value);
    }

    /**
     * @return int
     */
    public function &getValue() : int{
        return parent::getValue();
    }

    /**
     * @param int $value
     *
     * @throws \TypeError
     */
    public function setValue($value){
        if(!is_int($value)){
            throw new \TypeError("IntTag value must be of type int, " . gettype($value) . " given");
        }elseif($value < -(2 ** 31) or $value > ((2 ** 31) - 1)){
            throw new \InvalidArgumentException("Value $value is too large!");
        }
        parent::setValue($value);
    }
}