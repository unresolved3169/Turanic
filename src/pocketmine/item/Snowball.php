<?php

/*
 *
 * _______  _
 *   |__   __|   (_)
 *   | |_   _ _ __ __ _ _ __  _  ___
 *   | | | | | '__/ _` | '_ \| |/ __|
 *   | | |_| | | | (_| | | | | | (__
 *   |_|\__,_|_|  \__,_|_| |_|_|\___|
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

namespace pocketmine\item;


class Snowball extends ProjectileItem {
	/**
	 * Snowball constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::SNOWBALL, 0, $count, "Snowball");
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize() : int{
		return 16;
	}

 public function getProjectileEntityType() : string{
  return "Snowball";
 }

 public function getThrowForce() : float{
  return 1.5;
 }
}