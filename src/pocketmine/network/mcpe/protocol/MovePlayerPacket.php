<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


use pocketmine\math\Vector3;

class MovePlayerPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::MOVE_PLAYER_PACKET;

	const MODE_NORMAL = 0;
	const MODE_RESET = 1;
	const MODE_TELEPORT = 2;
	const MODE_ROTATION = 3;

    /** @var int */
    public $entityRuntimeId;
    /** @var Vector3 */
    public $position;
    /** @var float */
    public $yaw;
    /** @var float */
    public $bodyYaw;
    /** @var float */
    public $pitch;
    /** @var bool */
    public $onGround = false;
    /** @var bool */
    public $teleported = false;

	protected function decodePayload(){
        $this->entityRuntimeId = $this->getEntityRuntimeId();
        $this->position = $this->getVector3Obj();
        $this->pitch = $this->getByteRotation();
        $this->bodyYaw = $this->getByteRotation();
        $this->yaw = $this->getByteRotation();
        $this->onGround = $this->getBool();
        $this->teleported = $this->getBool();
	}

	protected function encodePayload(){
        $this->putEntityRuntimeId($this->entityRuntimeId);
        $this->putVector3Obj($this->position);
        $this->putByteRotation($this->pitch);
        $this->putByteRotation($this->bodyYaw);
        $this->putByteRotation($this->yaw);
        $this->putBool($this->onGround);
        $this->putBool($this->teleported);
	}
}