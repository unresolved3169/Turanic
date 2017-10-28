<?php

/*
 *
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
 *
*/

namespace pocketmine\command\overload;

class CommandParameter{
	
	const FLAG_VALID = 1048576;
	const FLAG_ENUM = 2097152;
	const FLAG_POSTFIX = 16777216;
	const FLAG_TEMPLATE = 16777216;
	
	const TYPE_UNKNOWN = 0;
	const TYPE_INT = 1;
	const TYPE_FLOAT = 2;
	const TYPE_VALUE = 3;
	const TYPE_MIXED = 3;
	const TYPE_TARGET = 4;
	const TYPE_STRING = 13;
	const TYPE_POSITION = 14;
	const TYPE_RAWTEXT = 17;
	const TYPE_TEXT = 19;
	const TYPE_JSON = 22;
	const TYPE_COMMAND = 29;
	
	protected $name;
	protected $type;
	protected $optional;
	protected $enum;
	protected $flag;
	protected $postfix;
	
	public function __construct(string $name, int $type = self::TYPE_STRING, bool $optional = true, int $flag = self::FLAG_VALID, CommandEnum $enum = null, string $postfix = ""){
		$this->name = $name;
		$this->type = $type;
		$this->enum = $enum;
		$this->optional = $optional;
		$this->flag = $flag;
		$this->postfix = $postfix;
	}
	
	public function getName() : string{
		return $this->name;
	}
	
	public function getType() : int{
		return $this->type;
	}
	
	public function getEnum(){
		return $this->enum;
	}
	
	public function getPostfix(){
		return $this->postfix;
	}
	
	public function isOptional() : bool{
		return $this->optional;
	}
	
	public function getFlag() : int{
		return $this->flag;
	}
	
	public static function getTypeFromString(string $str) : int{
		switch(strtolower($str)){
			case "int":
			 return self::TYPE_INT;
			case "float":
			 return self::TYPE_FLOAT;
			case "mixed":
			case "value":
			 return self::TYPE_VALUE;
			case "target":
			 return self::TYPE_TARGET;
			case "string":
			 return self::TYPE_STRING;
			case "pos":
			case "position":
			 return self::TYPE_POSITION;
			case "rawtext":
			case "raw_text":
			 return self::TYPE_RAWTEXT;
			case "text":
			 return self::TYPE_TEXT;
			case "json":
			 return self::TYPE_JSON;
			case "command":
			 return self::TYPE_COMMAND;
		}
		return self::TYPE_UNKNOWN;
	}
	
	public static function getFlagFromString(string $str) : int{
		switch(strtolower($str)){
			case "valid":
			 return self::FLAG_VALID;
			case "enum":
			case "list":
			 return self::FLAG_ENUM;
			case "postfix":
			case "template":
			 return self::FLAG_POSTFIX;
		}
		return self::FLAG_VALID;
	}
}
?>