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

namespace pocketmine\inventory;

use pocketmine\tile\Hopper;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class HopperInventory extends ContainerInventory {

    /** @var Hopper */
    protected $holder;

	public function __construct(Hopper $tile){
		parent::__construct($tile);
	}
	
	public function getName() : string{
		return "Hopper";
	}
	
	public function getDefaultSize() : int{
		return 5;
	}

	/**
	 * @return Hopper
	 */
	public function getHolder(){
		return $this->holder;
	}
	
	public function getNetworkType() : int{
		return WindowTypes::HOPPER;
	}
}