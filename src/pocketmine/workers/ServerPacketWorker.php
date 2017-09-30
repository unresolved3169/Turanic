<?php

/*
 *
 * _______  _
 *   |__   __|   (_)
 *   | |_   _ _ __ __ _ _ __  _  ___
 *   | | | | | '__/ _` | '_ \| |/ __|
 *   | | |_| | | | (_| | | | | | (__
 *   |_|\__,_|_|  \__,_|_| |_|_|\___|
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

namespace pocketmine\workers;

use pocketmine\utils\Binary;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\Worker;
use pocketmine\Server;

/**
 * Based on Steadfast2
 */

class ServerPacketWorker extends Worker {


	protected $classLoader;
	protected $shutdown;
	
	protected $externalQueue;
	protected $internalQueue;	

	public function __construct(\ClassLoader $loader = null) {
		$this->externalQueue = new \Threaded;
		$this->internalQueue = new \Threaded;
		$this->shutdown = false;
		$this->classLoader = $loader;
		$this->start(PTHREADS_INHERIT_CONSTANTS);
	}
	
	public function run() {
		$this->registerClassLoader();
		gc_enable();
		ini_set("memory_limit", -1);
		ini_set("display_errors", 1);
		ini_set("display_startup_errors", 1);
		
		$this->tickProcessor();
	}

	public function pushMainToThreadPacket($data) {
		$this->internalQueue[] = $data;
	}

	public function readMainToThreadPacket() {
		return $this->internalQueue->shift();
	}
	
	public function readThreadToMainPacket() {
		return $this->externalQueue->shift();
	}

	protected function tickProcessor() {
		while (!$this->shutdown) {			
			$start = microtime(true);
			$this->tick();
			$time = microtime(true) - $start;
			if ($time < 0.024) {
				@time_sleep_until(microtime(true) + 0.025 - $time);
			}
		}
	}

	protected function tick(){				
		while(count($this->internalQueue) > 0){
			$data = unserialize($this->readMainToThreadPacket());
			$this->handlePacket($data);
		}
	}
	
	protected function handlePacket(array $data){
		$compressionLevel = (int) ($data["compressionLevel"] ?? 7);
		$packets = $data["packets"] ?? [];
		$ack = (bool) $data["needACK"] ?? false;
		$immediate = (bool) $data["immediate"] ?? false;
		$targets = $data["targets"] ?? [];
		
		$payload = "";
		
		foreach($packets as $pk){
			$payload .= Binary::writeUnsignedVarInt(strlen($pk)) . $pk;
		}
		
		$buffer = zlib_encode($payload, ZLIB_ENCODING_DEFLATE, $compressionLevel);
		
		$this->externalQueue[] = $this->makeIdentifier($targets, $buffer, $ack, $immediate);
	}
	
	private function makeIdentifier(array $targets, $payload, bool $needACK = false, bool $immediate = false){
		return serialize([
		"payload" => $payload,
		"needACK" => $needACK,
		"immediate" => $immediate,
		"targets" => $targets]);
	}
	
	public function shutdown(){		
		$this->shutdown = true;
	}
}