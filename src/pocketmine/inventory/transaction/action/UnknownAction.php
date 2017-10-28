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

declare(strict_types=1);

namespace pocketmine\inventory\transaction\action;

use pocketmine\Player;
use pocketmine\item\Item;

/**
 * Receives Unknown Action
 */
class UnknownAction extends InventoryAction{
	
	public function __construct(){
		parent::__construct(Item::get(Item::AIR), Item::get(Item::AIR));
	}
	
	public function isValid(Player $source) : bool{
		return true;
	}
	
	public function execute(Player $source) : bool{
		return true;
	}
	
	public function onExecuteSuccess(Player $player){
		
	}
	
	public function onExecuteFail(Player $player){
		
	}
}