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


abstract class NamedTag extends Tag{
    /** @var string */
    protected $__name;

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __construct(string $name = "", $value = null){
        $this->__name = ($name === null or $name === false) ? "" : $name;
        if($value !== null){
            $this->setValue($value);
        }
    }

    /**
     * @return string
     */
    public function getName() : string{
        return $this->__name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name){
        $this->__name = $name;
    }
}