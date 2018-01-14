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

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>


#ifndef COMPILE
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\utils\Binary;
#endif

class BatchPacket extends DataPacket{
	const NETWORK_ID = 0xfe;

	/** @var string */
	public $payload = "";
	/** @var int */
	protected $compressionLevel = 7;

	public function canBeBatched() : bool{
		return false;
	}

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	protected function decodeHeader(){
		$pid = $this->getByte();
		assert($pid === static::NETWORK_ID);
	}

	protected function decodePayload(){
		$data = $this->getRemaining();
		try{
			$this->payload = zlib_decode($data, 1024 * 1024 * 64); //Max 64MB
		}catch(\ErrorException $e){ //zlib decode error
			$this->payload = "";
		}
	}

	protected function encodeHeader(){
		$this->putByte(static::NETWORK_ID);
	}

	protected function encodePayload(){
		$this->put(zlib_encode($this->payload, ZLIB_ENCODING_DEFLATE, $this->compressionLevel));
	}

	/**
	 * @param DataPacket|string $packet
	 */
	public function addPacket($packet){
        if($packet instanceof DataPacket) {
            if (!$packet->canBeBatched()) {
                throw new \InvalidArgumentException(get_class($packet) . " cannot be put inside a BatchPacket");
            }
            if (!$packet->isEncoded) {
                $packet->encode();
            }
        }

        $buf = ($packet instanceof DataPacket) ? $packet->buffer : $packet;
        $this->payload .= Binary::writeUnsignedVarInt(strlen($buf)) . $buf;
	}

	/**
	 * @return \Generator
	 */
	public function getPackets(){
		$stream = new NetworkBinaryStream($this->payload);
		while(!$stream->feof()){
			yield $stream->getString();
		}
	}

	public function getCompressionLevel() : int{
		return $this->compressionLevel;
	}

	public function setCompressionLevel(int $level){
		$this->compressionLevel = $level;
	}

}