<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\inventory;

use pocketmine\tile\Hopper;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class HopperInventory extends ContainerInventory {

    /** @var Hopper */
    protected $holder;

	/**
	 * HopperInventory constructor.
	 *
	 * @param Hopper $tile
	 */
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