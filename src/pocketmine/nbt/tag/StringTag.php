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

class StringTag extends NamedTag{

    /**
     * StringTag constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name = "", string $value = ""){
        parent::__construct($name, $value);
    }

    public function getType() : int{
        return NBT::TAG_String;
    }

    public function read(NBTStream $nbt){
        $this->value = $nbt->getString();
    }

    public function write(NBTStream $nbt){
        $nbt->putString($this->value);
    }

    /**
     * @return string
     */
    public function &getValue() : string{
        return parent::getValue();
    }

    /**
     * @param string $value
     *
     * @throws \TypeError
     */
    public function setValue($value){
        if(!is_string($value)){
            throw new \TypeError("StringTag value must be of type string, " . gettype($value) . " given");
        }
        parent::setValue($value);
    }
}