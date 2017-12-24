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

class ByteArrayTag extends NamedTag {

    /**
     * ByteArrayTag constructor.
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name = "", string $value = ""){
        parent::__construct($name, $value);
    }

    /**
	 * @return int
	 */
	public function getType(): int{
		return NBT::TAG_ByteArray;
	}

	/**
	 * @param NBT  $nbt
	 * @param bool $network
	 *
	 * @return mixed|void
	 */
	public function read(NBT $nbt, bool $network = false){
		$this->value = $nbt->get($nbt->getInt($network));
	}

	/**
	 * @param NBT  $nbt
	 * @param bool $network
	 *
	 * @return mixed|void
	 */
	public function write(NBT $nbt, bool $network = false){
		$nbt->putInt(strlen($this->value), $network);
		$nbt->put($this->value);
	}

	public function &getValue(){
        return parent::getValue();
    }

    public function setValue($value){
        if (!is_string($value)) {
            throw new \TypeError("ByteArrayTag value must be of type string, " . gettype($value) . " given");
        }
        parent::setValue($value);
    }
}