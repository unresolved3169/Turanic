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
use pocketmine\form\element\Button;

class SimpleForm extends Form{
	
	protected $content = '';
	protected $buttons = [];
	
	public function __construct(string $title, string $content = ''){
		parent::__construct($title);
		$this->content = $content;
	}
	
	public function addButton(Button $button){
		$this->buttons[] = $button;
	}
	
	public function getButtons() : array{
		return $this->buttons;
	}

	public function jsonSerialize(){
		$data = parent::jsonSerialize();
		
		$data["content"] = $this->content;
		$data["buttons"] = $this->buttons;
		
		return $data;
	}
	
	public function handleResponse($response, Player $player){
		return isset($this->buttons[$response]) ? $this->buttons[$response]->handleResponse($response, $player) : null;
	}
	
	public function getType() : string{
		return self::TYPE_SIMPLE;
	}
}