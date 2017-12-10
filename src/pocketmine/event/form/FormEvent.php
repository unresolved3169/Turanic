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

namespace pocketmine\event\form;

use pocketmine\form\Form;
use pocketmine\Player;
use pocketmine\event\Event;

abstract class FormEvent extends Event{

	protected $player;
	protected $form;

	public function __construct(Player $player, Form $form){
		$this->form = $form;
		$this->player = $player;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function getForm(){
		return $this->form;
	}
	
	public function setForm(Form $form){
		$this->form = $form;
	}
}
