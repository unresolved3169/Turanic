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

namespace pocketmine\level\format\io;

use pocketmine\level\LevelException;

abstract class LevelProviderManager{
    protected static $providers = [];

    /**
     * @param string $class
     *
     * @throws LevelException
     */
    public static function addProvider(string $class){
        if(!is_subclass_of($class, LevelProvider::class)){
            throw new LevelException("Class is not a subclass of LevelProvider");
        }
        /** @var LevelProvider $class */
        self::$providers[strtolower($class::getProviderName())] = $class;
    }

    /**
     * Returns a LevelProvider class for this path, or null
     *
     * @param string $path
     *
     * @return string|null
     */
    public static function getProvider(string $path){
        foreach(self::$providers as $provider){
            /** @var $provider LevelProvider */
            if($provider::isValid($path)){
                return $provider;
            }
        }

        return null;
    }

    /**
     * Returns a LevelProvider by name, or null if not found
     *
     * @param string $name
     *
     * @return string|null
     */
    public static function getProviderByName(string $name){
        return self::$providers[trim(strtolower($name))] ?? null;
    }
}