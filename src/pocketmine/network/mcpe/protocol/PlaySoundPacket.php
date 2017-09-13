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

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

class PlaySoundPacket extends DataPacket {

	const NETWORK_ID = ProtocolInfo::PLAY_SOUND_PACKET;

	public $sound;
	public $x;
	public $y;
	public $z;
	public $volume = 1.0;
	public $pitch = 1.0;

	/**
	 *
	 */
	public function decode(){

	}

	/**
	 *
	 */
	public function encode(){
        $this->reset();
        $this->putString($this->sound);
        $this->putBlockCoords($this->x * 8, $this->y * 8, $this->z * 8);
        $this->putLFloat($this->volume);
        $this->putLFloat($this->pitch);
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "PlaySoundPacket";
	}

}
