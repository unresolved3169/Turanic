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

namespace pocketmine\item;

abstract class MusicDisc extends Item{
	
	const NO_RECORD = 0;

    /**
     * MusicDisc constructor.
     *
     * @param int $discId
     * @param string $name
     */
	public function __construct($discId, $name = "Music Disc"){
		parent::__construct($this->verifyDisc($discId), 0, $name);
	}
	
	public function verifyDisc(int $discId) : int{
		if($discId >= 500 and $discId <= 511){
			return $discId;
		}
		return 500;
	}
	
	public function getRecordId() : int{
		return 90 + ($this->id - 500);
	}

	public function getSoundId(){
        return 90 + ($this->getRecordId() - 2256);
    }
	
	public function getRecordName() : string{
		return str_ireplace("Music Disc ", "", $this->getName()); // to easy :D
	}
}