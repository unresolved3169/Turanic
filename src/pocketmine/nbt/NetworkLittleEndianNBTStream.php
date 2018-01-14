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

namespace pocketmine\nbt;

#ifndef COMPILE
use pocketmine\utils\Binary;
#endif

#include <rules/NBT.h>

class NetworkLittleEndianNBTStream extends LittleEndianNBTStream{

    public function getInt() : int{
        return Binary::readVarInt($this->buffer, $this->offset);
    }

    public function putInt(int $v){
        $this->put(Binary::writeVarInt($v));
    }

    public function getLong() : int{
        return Binary::readVarLong($this->buffer, $this->offset);
    }

    public function putLong(int $v){
        $this->put(Binary::writeVarLong($v));
    }

    public function getString() : string{
        return $this->get(Binary::readUnsignedVarInt($this->buffer, $this->offset));
    }

    public function putString(string $v){
        $this->put(Binary::writeUnsignedVarInt(strlen($v)) . $v);
    }

    public function getIntArray() : array{
        $len = $this->getInt(); //varint
        $ret = [];
        for($i = 0; $i < $len; ++$i){
            $ret[] = $this->getInt(); //varint
        }

        return $ret;
    }

    public function putIntArray(array $array){
        $this->putInt(count($array)); //varint
        foreach($array as $v){
            $this->putInt($v); //varint
        }
    }
}