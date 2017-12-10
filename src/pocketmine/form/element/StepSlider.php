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

class StepSlider extends FormElement{
	
	protected $steps = [];
	protected $defaultStepIndex = 0;
	
	public function __construct($text, $steps = []){
		$this->text = $text;
		$this->steps = $steps;
	}
	
	public function addStep(string $stepText, bool $isDefault = false){
		if ($isDefault){
			$this->defaultStepIndex = count($this->steps);
		}
		$this->steps[] = $stepText;
	}
	
	public function setStepAsDefault(string $stepText){
		$index = array_search($stepText, $this->steps);
		if ($index === false){
			return false;
		}
		$this->defaultStepIndex = $index;
		return true;
	}
	
	public function setSteps(array $steps){
		$this->steps = $steps;
	}
	
	public function getStep(int $index) : string{
		return $this->steps[$index] ?? null;
	}
	
	public function jsonSerialize(){
		$data = parent::jsonSerialize();
		$data["steps"] = array_map('strval', $this->steps);
		$data["default"] = $this->defaultStepIndex;
		return $data;
	}
	
	public function handleResponse($value, Player $player){
		return $this->steps[$value];
	}
	
	public function getType() : string{
		return self::TYPE_STEP_SLIDER;
	}
}