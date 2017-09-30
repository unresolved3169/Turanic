<?php

namespace pocketmine\utils;

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

/**
 * from Steadfast2
 */

class Cache{
	
	private static $cache = [];
	
	public static function add($key, $value){
		self::$cache[$key] = $value;
	}
	
	public static function remove($key){
		unset(self::$cache[$key]);
	}
	
	public static function get($key){
		return self::$cache[$key] ?? null;
	}
	
	public static function clearAll(){
		self::$cache = [];
	}
}
?>