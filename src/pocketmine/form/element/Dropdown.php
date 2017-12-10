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

namespace pocketmine\form\element;

use pocketmine\Player;

class Dropdown extends FormElement{
	
	protected $options = [];
	protected $defaultOptionIndex = 0;
	
	public function __construct($text, $options = []){
		parent::__construct($text);
		$this->options = $options;
	}
	
	public function addOption(string $optionText, bool $isDefault = false){
		if ($isDefault){
			$this->defaultOptionIndex = count($this->options);
		}
		$this->options[] = $optionText;
	}
	
	public function setOptionAsDefault(string $optionText){
		$index = array_search($optionText, $this->options);
		if ($index === false){
			return false;
		}
		$this->defaultOptionIndex = $index;
		return true;
	}
	
	public function setOptions(array $options){
		$this->options = $options;
	}
	
	public function jsonSerialize(){
		$data = parent::jsonSerialize();
		
		$data["options"] = $this->options;
		$dafa["default"] = $this->defaultOptionIndex;
		
		return $data;
	}
	
	public function handleResponse($value, Player $player){
		return $this->options[$value];
	}
	
	public function getType() : string{
		return self::TYPE_DROPDOWN;
	}
}