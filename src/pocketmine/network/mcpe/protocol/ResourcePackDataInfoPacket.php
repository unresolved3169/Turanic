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

class ResourcePackDataInfoPacket extends DataPacket{
    const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_DATA_INFO_PACKET;

    const MAX_CHUNK_SIZE = 1048576; // 1MB

    /** @var string */
    public $packId;
    /** @var int */
    public $fileSize;
    /** @var string */
    public $sha256;

    protected function decodePayload(){ // TODO : Test et kaldırmayı
        $this->packId = $this->getString();
        $this->maxChunkSize = $this->getLInt();
        $this->chunkCount = $this->getLInt();
        $this->compressedPackSize = $this->getLLong();
        $this->sha256 = $this->getString();
    }

    protected function encodePayload(){
        $this->putString($this->packId);
        $this->putLInt(self::MAX_CHUNK_SIZE);
        $this->putLInt((int) ceil($this->fileSize / self::MAX_CHUNK_SIZE));
        $this->putLLong($this->fileSize);
        $this->putString($this->sha256);
    }
}