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

class Button extends FormElement{

	const IMAGE_TYPE_PATH = 'path';
	const IMAGE_TYPE_URL = 'url';
	
	protected $imageType;
	protected $image;
	
	public function setImage(string $imageType, string $image){
		if ($imageType != self::IMAGE_TYPE_PATH and $imageType != self::IMAGE_TYPE_URL){
			return false;
		}
		$this->imageType = $imageType;
		$this->image = $image;
	}
	
	 public function jsonSerialize(){
		$data = parent::jsonSerialize();
		
		if ($this->imageType != ""){
			$data['image'] = [
				'type' => $this->imageType,
				'data' => $this->image
			];
		}
		return $data;
	}
	
	public function handleResponse($value, Player $player){
		return $this->text;
	}
	
	public function getType() : string{
		return self::TYPE_BUTTON;
	}
	
	public function getImageType() : string{
		return $this->imageType;
	}
	
	public function getImage() : string{
		return $this->image;
	}

}