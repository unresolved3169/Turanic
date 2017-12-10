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

namespace pocketmine\form;

use pocketmine\Player;
use pocketmine\form\element\FormElement;

class CustomForm extends Form{
	
	protected $elements = [];
	protected $iconURL = '';
	
	public function addElement(FormElement $element){
		$this->elements[] = $element;
	}
	
	public function setIconUrl(string $url){
		$this->iconURL = $url;
	}

	public function jsonSerialize(){
		$data = parent::jsonSerialize();
		if (strlen($this->iconURL) > 0){
			$data['icon'] = [
				"type" => "url",
				"data" => $this->iconURL
			];
		}
		$data["content"] = $this->elements;
		return $data;
	}
	
	public function handleResponse($response, Player $player){
		$return = [];
		foreach ($response as $elementKey => $elementValue){
			if (isset($this->elements[$elementKey])){
				$return[] = $this->elements[$elementKey]->handleResponse($elementValue, $player);
			}
		}
		return $return;
	}
	
	public function getElements() : array{
		return $this->elements;
	}
	
	public function getType() : string{
		return self::TYPE_CUSTOM;
	}
}