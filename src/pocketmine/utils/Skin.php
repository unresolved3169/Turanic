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

/**
 * Player Skin Identifier
 */

namespace pocketmine\utils;

class Skin{
	
	const SKIN_CUSTOM = "Standart_Custom";
	const SKIN_ALEX = "Standart_Alex";
	
	protected $skinName;
	protected $skinData;
	protected $capeData;
	protected $geometryName;
	protected $geometryData;
	
	public function __construct(string $skinName = self::SKIN_CUSTOM, string $skinData, string $capeData = "", string $geometryName = "", string $geometryData = ""){
		$this->skinName = $skinName;
		$this->skinData = $skinData;
		$this->capeData = $capeData;
		$this->geometryName = $geometryName;
		$this->geometryData = $geometryData;
	}
	
	public function getSkinId() : string{
		return $this->skinName;
	}
	
	public function getSkinName() : string{
		return $this->skinName;
	}
	
	public function getSkinData() : string{
		return $this->skinData;
	}
	
	public function getCapeData() : string{
		return $this->capeData;
	}
	
	public function getGeometryName() : string{
		return $this->geometryName;
	}
	
	public function getGeometryData() : string{
		return $this->geometryData;
	}
	
	public function setSkinName(string $str){
		$this->skinName = $str;
	}
	
	public function setSkinData(string $str){
		$this->skinData = $str;
	}
	
	public function setCapeData(string $str){
		$this->capeData = $str;
	}
	
	public function setGeometryName(string $str){
		$this->geometryName = $str;
	}
	
	public function setGeometryData(string $str){
		$this->geometryData = $str;
	}
	
	public function isValid() : bool{
		return strlen($this->skinData) === 64 * 64 * 4 or strlen($this->skinData) === 64 * 32 * 4;
	}
}
?>