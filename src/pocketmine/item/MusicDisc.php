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

namespace pocketmine\item;


abstract class MusicDisc extends Item{
	
	const NO_RECORD = 0;
	const RECORD_13 = 2256;
	const RECORD_CAT = 2257;
	const RECORD_BLOCKS = 2258;
	const RECORD_CHIRP = 2259;
	const RECORD_FAR = 2260;
	const RECORD_MALL = 2261;
	const RECORD_MELLOHI = 2262;
	const RECORD_STAL = 2263;
	const RECORD_STRAD = 2264;
	const RECORD_WARD = 2265;
	const RECORD_11 = 2266;
	const RECORD_WAIT = 2267;
	
	/**
	 * MusicDisc constructor.
	 *
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($discId, $name = "Music Disc"){
		parent::__construct($this->verifyDisc($discId), 0, 1, $name);
	}
	
	public function verifyDisc(int $discId) : int{
		if($discId >= 500 and $discId <= 511){
			return $discId;
		}
		return 500;
	}
	
	public function getRecordId() : int{
		return 2256 + ($this->id - 500);
	}
	
	public function getRecordName() : string{
		return str_ireplace("Music Disc ", "", $this->getName()); // to easy :D
	}
}