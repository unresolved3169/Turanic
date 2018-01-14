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

class LittleEndianNBTStream extends NBTStream{

    public function getShort() : int{
        return Binary::readLShort($this->get(2));
    }

    public function getSignedShort() : int{
        return Binary::readSignedLShort($this->get(2));
    }

    public function putShort(int $v){
        $this->put(Binary::writeLShort($v));
    }

    public function getInt() : int{
        return Binary::readLInt($this->get(4));
    }

    public function putInt(int $v){
        $this->put(Binary::writeLInt($v));
    }

    public function getLong() : int{
        return Binary::readLLong($this->get(8));
    }

    public function putLong(int $v){
        $this->put(Binary::writeLLong($v));
    }

    public function getFloat() : float{
        return Binary::readLFloat($this->get(4));
    }

    public function putFloat(float $v){
        $this->put(Binary::writeLFloat($v));
    }

    public function getDouble() : float{
        return Binary::readLDouble($this->get(8));
    }

    public function putDouble(float $v){
        $this->put(Binary::writeLDouble($v));
    }

    public function getIntArray() : array{
        $len = $this->getInt();
        return array_values(unpack("V*", $this->get($len * 4)));
    }

    public function putIntArray(array $array){
        $this->putInt(count($array));
        $this->put(pack("V*", ...$array));
    }
}