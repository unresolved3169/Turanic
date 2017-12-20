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

/**
 * All the NBT Tags
 */

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;

abstract class Tag extends \stdClass {

	protected $value;

	/**
	 * @return mixed
	 */
	public function &getValue(){
		return $this->value;
	}

	/**
	 * @return int
	 */
	public abstract function getType(): int;

	/**
	 * @param $value
	 */
	public function setValue($value){
		$this->value = $value;
	}

	/**
	 * @param NBT  $nbt
	 * @param bool $network
	 *
	 * @return mixed
	 */
	abstract public function write(NBT $nbt, bool $network = false);

	/**
	 * @param NBT  $nbt
	 * @param bool $network
	 *
	 * @return mixed
	 */
	abstract public function read(NBT $nbt, bool $network = false);

	/**
	 * @return string
	 */
	public function __toString(){
		return (string) $this->value;
	}
}
