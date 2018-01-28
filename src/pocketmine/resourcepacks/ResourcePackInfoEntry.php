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

namespace pocketmine\resourcepacks;

class ResourcePackInfoEntry{
    protected $packId; //UUID
    protected $version;
    protected $packSize;

    public function __construct(string $packId, string $version, int $packSize = 0){
        $this->packId = $packId;
        $this->version = $version;
        $this->packSize = $packSize;
    }

    public function getPackId() : string{
        return $this->packId;
    }

    public function getVersion() : string{
        return $this->version;
    }

    public function getPackSize() : int{
        return $this->packSize;
    }

}