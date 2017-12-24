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

#include <rules/NBT.h>

class ShortTag extends NamedTag {

    /**
	 * @return int
	 */
	public function getType(): int{
		return NBT::TAG_Short;
	}

	/**
	 * @param NBT  $nbt
	 * @param bool $network
	 *
	 * @return mixed|void
	 */
	public function read(NBT $nbt, bool $network = false){
		$this->value = $nbt->getShort();
	}

	/**
	 * @param NBT  $nbt
	 * @param bool $network
	 *
	 * @return mixed|void
	 */
	public function write(NBT $nbt, bool $network = false){
		$nbt->putShort($this->value);
	}

	public function &getValue(){
        return parent::getValue();
    }

    /**
     * @param int $value
     *
     * @throws \TypeError
     */
    public function setValue($value){
        if (!is_int($value)){
            throw new \TypeError("ShortTag value must be of type int, " . gettype($value) . " given");
        }elseif ($value < -(2 ** 15) or $value > ((2 ** 15) - 1)) {
            throw new \InvalidArgumentException("Value $value is too large!");
        }
        parent::setValue($value);
    }
}