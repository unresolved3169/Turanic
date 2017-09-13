<?php

/*
 *
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
 *
*/

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


class MoveEntityPacket extends DataPacket {

	const NETWORK_ID = ProtocolInfo::MOVE_ENTITY_PACKET;

	public $eid;
	public $x;
	public $y;
	public $z;
	public $yaw;
	public $headYaw;
	public $pitch;
	public $onGround = true;
	public $teleport = false;

	/**
	 *
	 */
	public function decode(){
		$this->eid = $this->getEntityId();
		$this->getVector3f($this->x, $this->y, $this->z);
		$this->pitch = $this->getByte() * (360.0 / 256);
		$this->yaw = $this->getByte() * (360.0 / 256);
		$this->headYaw = $this->getByte() * (360.0 / 256);
		$this->onGround = $this->getBool();
		$this->teleport = $this->getBool();
	}

	/**
	 *
	 */
	public function encode(){
		$this->reset();
		$this->putEntityId($this->eid);
		$this->putVector3f($this->x, $this->y, $this->z);
		$this->putByte($this->pitch / (360.0 / 256));
		$this->putByte($this->yaw / (360.0 / 256));
		$this->putByte($this->headYaw / (360.0 / 256));
		$this->putBool($this->onGround);
		$this->putBool($this->teleport);
	}

}
