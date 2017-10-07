<?php

namespace pocketmine\command\overload;

class CommandParameter{
	
	const FLAG_VALID = 0x100000;
	const FLAG_ENUM = 0x200000;
	const FLAG_POSTFIX = 0x1000000;
	
	const TYPE_INT = 0x01;
	const TYPE_FLOAT = 0x02;
	const TYPE_VALUE = 0x03;
	const TYPE_TARGET = 0x04;
	const TYPE_STRING = 0x0d;
	const TYPE_POSITION = 0x0e;
	const TYPE_RAWTEXT = 0x11;
	const TYPE_TEXT = 0x13;
	const TYPE_JSON = 0x16;
	const TYPE_COMMAND = 0x1d;
	
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
			 return self::TYPE_MIXED;
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
			 return self::FLAG_POSTFIX;
		}
		return self::FLAG_VALID;
	}
}
?>