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

class Input extends FormElement{

	protected $placeholder = '';
	protected $defaultText = '';
	
	public function __construct($text, $placeholder, $defaultText = ''){
		parent::__construct($text);
		$this->placeholder = $placeholder;
		$this->defaultText = $defaultText;
	}
	
	public function jsonSerialize(){
		$data = parent::jsonSerialize();
		$data["placeholder"] = $this->placeholder;
		$data["default"] = $this->defaultText;
		return $data;
	}
	
	public function handleResponse($value, Player $player){
		return $value;
	}
	
	public function getType() : string{
		return self::TYPE_INPUT;
	}

}