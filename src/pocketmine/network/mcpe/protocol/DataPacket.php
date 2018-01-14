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

use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\utils\Utils;

abstract class DataPacket extends NetworkBinaryStream{

	const NETWORK_ID = 0;

	/** @var bool */
	public $isEncoded = false;

	/** @var int */
	public $extraByte1 = 0;
	/** @var int */
	public $extraByte2 = 0;

	public function pid(){
		return $this::NETWORK_ID;
	}

	public function getName() : string{
		return (new \ReflectionClass($this))->getShortName();
	}

	public function canBeBatched() : bool{
		return true;
	}

	public function canBeSentBeforeLogin() : bool{
		return false;
	}

	public function decode(){
		$this->offset = 0;
		$this->decodeHeader();
		$this->decodePayload();
	}

	protected function decodeHeader(){
		$pid = $this->getUnsignedVarInt();
		assert($pid === static::NETWORK_ID);

		$this->extraByte1 = $this->getByte();
		$this->extraByte2 = $this->getByte();
		assert($this->extraByte1 === 0 and $this->extraByte2 === 0, "Got unexpected non-zero split-screen bytes (byte1: $this->extraByte1, byte2: $this->extraByte2");
	}

	/**
	 * Note for plugin developers: If you're adding your own packets, you should perform decoding in here.
	 */
	protected function decodePayload(){

	}

	public function encode(){
		$this->reset();
		$this->encodeHeader();
		$this->encodePayload();
		$this->isEncoded = true;
	}

	protected function encodeHeader(){
		$this->putUnsignedVarInt(static::NETWORK_ID);

		$this->putByte($this->extraByte1);
		$this->putByte($this->extraByte2);
	}

	/**
	 * Note for plugin developers: If you're adding your own packets, you should perform encoding in here.
	 */
	protected function encodePayload(){

	}

	public function clean(){
		$this->buffer = null;
		$this->isEncoded = false;
		$this->offset = 0;
		return $this;
	}

	public function __debugInfo(){
		$data = [];
		foreach($this as $k => $v){
			if($k === "buffer" and is_string($v)){
				$data[$k] = bin2hex($v);
			}elseif(is_string($v) or (is_object($v) and method_exists($v, "__toString"))){
				$data[$k] = Utils::printable((string) $v);
			}else{
				$data[$k] = $v;
			}
		}

		return $data;
	}

    public function mayHaveUnreadBytes() : bool{
	    return false;
    }
}