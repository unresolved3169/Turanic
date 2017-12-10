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

abstract class FormElement implements \JsonSerializable{
	
	const TYPE_BUTTON = "button";
	const TYPE_DROPDOWN = "dropdown";
	const TYPE_IMAGE = "image";
	const TYPE_LABEL = "label";
	const TYPE_SLIDER = "slider";
	const TYPE_STEP_SLIDER = "step_slider";
	const TYPE_INPUT = "input";
	const TYPE_TOGGLE = "toggle";

	protected $text;
	
	public function __construct(string $text){
		$this->text = $text;
	}

	/**
	 * Returns an array of item stack properties that can be serialized to json.
	 *
	 * @return array
	 */
	public function jsonSerialize(){
		return ["type" => $this->getType(), "text" => $this->getText()];
	}

	/**
	 * @param $value
	 * @param Player $player
	 * @return mixed
	 */
	abstract public function handleResponse($value, Player $player);
	
	public function getText() : string{
		return $this->text;
	}
	
	public abstract function getType() : string;

}