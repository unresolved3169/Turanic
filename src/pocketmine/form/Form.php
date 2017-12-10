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

abstract class Form implements \JsonSerializable{
	
	const TYPE_SIMPLE = "form";
	const TYPE_MODAL = "modal";
	const TYPE_CUSTOM = "custom_form";
	
	protected $title;
	
	protected $formId = 0;
	
	public function __construct(string $title){
		$this->title = $title;
	}

	public abstract function handleResponse($response, Player $player);

	public function jsonSerialize(){
		return [
		"title" => $this->getTitle(),
		"type" => $this->getType()];
	}
	
	public function onClose(Player $player){
		
	}

	public function getTitle() : string{
		return $this->title;
	}
	
	public abstract function getType() : string;
	
	public function getId() : int{
		return $this->formId;
	}
	
	public function setId(int $id){
		$this->formId = $id;
	}
}