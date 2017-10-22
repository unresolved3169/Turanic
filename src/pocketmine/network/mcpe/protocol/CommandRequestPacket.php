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

class CommandRequestPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::COMMAND_REQUEST_PACKET;
	
	const TYPE_PLAYER = 0;
	const TYPE_COMMAND_BLOCK = 1;
	const TYPE_MINECART_COMMAND_BLOCK = 2;
	const TYPE_DEV_CONSOLE = 3;

	/** @var string */
	public $command;
	/** @var int */
	public $type;
	
	/** @var string */
	public $requestId;
	/** @var int */
	public $playerId;

	protected function decodePayload(){
		$this->command = $this->getString();
		$this->type = $this->getUnsignedVarInt();
		$this->requestId = $this->getString();
		$this->playerId = $this->getEntityRuntimeId();
	}
}