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

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;

class Banner extends Spawnable{

	const PATTERN_BOTTOM_STRIPE = "bs";
	const PATTERN_TOP_STRIPE = "ts";
	const PATTERN_LEFT_STRIPE = "ls";
	const PATTERN_RIGHT_STRIPE = "rs";
	const PATTERN_CENTER_STRIPE = "cs";
	const PATTERN_MIDDLE_STRIPE = "ms";
	const PATTERN_DOWN_RIGHT_STRIPE = "drs";
	const PATTERN_DOWN_LEFT_STRIPE = "dls";
	const PATTERN_SMALL_STRIPES = "ss";
	const PATTERN_DIAGONAL_CROSS = "cr";
	const PATTERN_SQUARE_CROSS = "sc";
	const PATTERN_LEFT_OF_DIAGONAL = "ld";
	const PATTERN_RIGHT_OF_UPSIDE_DOWN_DIAGONAL = "rud";
	const PATTERN_LEFT_OF_UPSIDE_DOWN_DIAGONAL = "lud";
	const PATTERN_RIGHT_OF_DIAGONAL = "rd";
	const PATTERN_VERTICAL_HALF_LEFT = "vh";
	const PATTERN_VERTICAL_HALF_RIGHT = "vhr";
	const PATTERN_HORIZONTAL_HALF_TOP = "hh";
	const PATTERN_HORIZONTAL_HALF_BOTTOM = "hhb";
	const PATTERN_BOTTOM_LEFT_CORNER = "bl";
	const PATTERN_BOTTOM_RIGHT_CORNER = "br";
	const PATTERN_TOP_LEFT_CORNER = "tl";
	const PATTERN_TOP_RIGHT_CORNER = "tr";
	const PATTERN_BOTTOM_TRIANGLE = "bt";
	const PATTERN_TOP_TRIANGLE = "tt";
	const PATTERN_BOTTOM_TRIANGLE_SAWTOOTH = "bts";
	const PATTERN_TOP_TRIANGLE_SAWTOOTH = "tts";
	const PATTERN_MIDDLE_CIRCLE = "mc";
	const PATTERN_MIDDLE_RHOMBUS = "mr";
	const PATTERN_BORDER = "bo";
	const PATTERN_CURLY_BORDER = "cbo";
	const PATTERN_BRICK = "bri";
	const PATTERN_GRADIENT = "gra";
	const PATTERN_GRADIENT_UPSIDE_DOWN = "gru";
	const PATTERN_CREEPER = "cre";
	const PATTERN_SKULL = "sku";
	const PATTERN_FLOWER = "flo";
	const PATTERN_MOJANG = "moj";

	const COLOR_BLACK = 0;
	const COLOR_RED = 1;
	const COLOR_GREEN = 2;
	const COLOR_BROWN = 3;
	const COLOR_BLUE = 4;
	const COLOR_PURPLE = 5;
	const COLOR_CYAN = 6;
	const COLOR_LIGHT_GRAY = 7;
	const COLOR_GRAY = 8;
	const COLOR_PINK = 9;
	const COLOR_LIME = 10;
	const COLOR_YELLOW = 11;
	const COLOR_LIGHT_BLUE = 12;
	const COLOR_MAGENTA = 13;
	const COLOR_ORANGE = 14;
	const COLOR_WHITE = 15;

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->Base) or !($nbt->Base instanceof IntTag)){
			$nbt->Base = new IntTag("Base", 15);
		}
		
		if(!isset($nbt->Patterns) or !($nbt->Patterns instanceof ListTag)){
			$nbt->Patterns = new ListTag("Patterns");
		}
		
		parent::__construct($level, $nbt);
	}
	
	public function getSpawnCompound(){
		$c = new CompoundTag("", [
			new StringTag("id", Tile::BANNER),
			new IntTag("x", (int)$this->x),
			new IntTag("y", (int)$this->y),
			new IntTag("z", (int)$this->z)
		]);
		
		if($this->hasName()){
			$c->CustomName = $this->namedtag->CustomName;
		}
		
		return $c;
	}
	
	public function addAdditionalSpawnData(CompoundTag $nbt){
		$nbt->Patterns = $this->namedtag->Patterns;
		$nbt->Base = $this->namedtag->Base;
	}
	
	public function getBaseColor(){
		return $this->namedtag->Base->getValue();
	}
	
	public function setBaseColor($color){
		$this->namedtag->Base->setValue($color & 0x0f);
		$this->onChanged();
	}
	
	public function getPatternIds(){
		$keys = array_keys((array) $this->namedtag->Patterns);

		foreach($keys as $key => $index){
			if(!is_numeric($index)){
				unset($keys[$key]);
			}
		}
		
		return $keys;
	}
	
	public function addPattern($pattern, $color){
		$patternId = 0;
		if($this->getPatternCount() !== 0) {
			$patternId = max($this->getPatternIds()) + 1;
		}

		$this->namedtag->Patterns->{$patternId} = new CompoundTag("", [
			new IntTag("Color", $color & 0x0f),
			new StringTag("Pattern", $pattern)
		]);

		$this->onChanged();
		
		return $patternId;
	}
	
	public function patternExists($patternId){
		return isset($this->namedtag->Patterns->{$patternId});
	}
	
	public function getPatternData($patternId){
		if(!$this->patternExists($patternId)){
			return [];
		}

		return [
			"Color" => $this->namedtag->Patterns->{$patternId}->Color->getValue(),
			"Pattern" => $this->namedtag->Patterns->{$patternId}->Pattern->getValue()
		];
	}
	
	public function changePattern($patternId, $pattern, $color){
		if(!$this->patternExists($patternId)){
			return true;
		}

		$this->namedtag->Patterns->{$patternId}->setValue([
			new IntTag("Color", $color & 0x0f),
			new StringTag("Pattern", $pattern)
		]);

		$this->onChanged();
		
		return true;
	}
	
	public function deletePattern($patternId){
		if(!$this->patternExists($patternId)){
			return true;
		}
		
		unset($this->namedtag->Patterns->{$patternId});

		$this->onChanged();
		
		return true;
	}
	
	public function deleteTopPattern(){
		$keys = $this->getPatternIds();
		if(empty($keys)){
			return true;
		}

		$index = max($keys);
		unset($this->namedtag->Patterns->{$index});

		$this->onChanged();
		
		return true;
	}
	
	public function deleteBottomPattern(){
		$keys = $this->getPatternIds();
		if(empty($keys)){
			return true;
		}

		$index = min($keys);
		unset($this->namedtag->Patterns->{$index});

		$this->onChanged();
		
		return true;
	}
	
	public function getPatternCount(){
		return count($this->getPatternIds());
	}
}
