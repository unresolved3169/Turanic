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

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\protocol\types\PlayerPermissions;

class AdventureSettingsPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::ADVENTURE_SETTINGS_PACKET;

	const PERMISSION_NORMAL = 0;
	const PERMISSION_OPERATOR = 1;
	const PERMISSION_HOST = 2;
	const PERMISSION_AUTOMATION = 3;
	const PERMISSION_ADMIN = 4;

	const WORLD_IMMUTABLE = 1;
	const NO_PVP = 2;
	const NO_PVM = 4;
	const NO_MVP = 8;
	const NO_EVP = 16;
	const AUTO_JUMP = 32;
	const ALLOW_FLIGHT = 64;
	const NO_CLIP = 128;
	const FLYING = 512;
	const MUTED = 1024;
	
	const WORLD_BUILDER = 0x100;

	const BUILD_AND_MINE = 1;
	const DOORS_AND_SWITCHES = 2;
	const OPEN_CONTAINERS = 4;
	const ATTACK_PLAYERS = 8;
	const ATTACK_MOBS = 16;
	const OPERATOR = 32;
	const TELEPORT = 64;

	/** @var int */
	public $flags = 0;
	/** @var int */
	public $commandPermission = self::PERMISSION_NORMAL;
	/** @var int */
	public $abilities = -1;
	/** @var int */
	public $playerPermission = PlayerPermissions::MEMBER;
	/** @var int */
	public $customPermissions = 0;
	/** @var int */
	public $entityUniqueId; //This is a little-endian long, NOT a var-long. (WTF Mojang)

	protected function decodePayload(){
		$this->flags = $this->getUnsignedVarInt();
		$this->commandPermission = $this->getUnsignedVarInt();
		$this->abilities = $this->getUnsignedVarInt();
		$this->playerPermission = $this->getUnsignedVarInt();
		$this->customPermissions = $this->getUnsignedVarInt();
		$this->entityUniqueId = $this->getLLong();
	}

	protected function encodePayload(){
		$this->putUnsignedVarInt($this->flags);
		$this->putUnsignedVarInt($this->commandPermission);
		$this->putUnsignedVarInt($this->abilities);
		$this->putUnsignedVarInt($this->playerPermission);
		$this->putUnsignedVarInt($this->customPermissions);
		$this->putLLong($this->entityUniqueId);
	}

	public function setPlayerFlag(int $flag, bool $value = true){
		if($value){
		 $this->flags |= $flag;
		}
	}
	
	public function getPlayerFlag(int $flag){
		return ($this->flags & $flag) !== 0;
	}
	
	public function setAbility(int $flag, bool $value = true){
		if($value){
		 $this->abilities |= $flag;
		}
	}
	
	public function getAbility(int $flag){
		return ($this->abilities & $flag) !== 0;
	}
	
	public function setCustomPermission(int $flag, bool $value = true){
		if($value){
		 $this->customPermissions |= $flag;
		}
	}
	
	public function getCustomPermission(int $flag){
		return ($this->customPermissions & $flag) !== 0;
	}
}