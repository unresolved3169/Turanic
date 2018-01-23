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

use pocketmine\item\Item;
use pocketmine\tile\BrewingStand;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class BrewingInventory extends ContainerInventory {

    /** @var BrewingStand */
    protected $holder;

	public function __construct(BrewingStand $tile){
		parent::__construct($tile);
	}
	
	public function getName() : string{
		return "Brewing";
	}
	
	public function getDefaultSize() : int{
		return 4;
	}

	public function getHolder(){
		return $this->holder;
	}

	/**
	 * @param Item $item
	 */
	public function setIngredient(Item $item){
		$this->setItem(0, $item);
	}

	/**
	 * @return Item
	 */
	public function getIngredient(){
		return $this->getItem(0);
	}

	/**
	 * @param int  $index
	 * @param Item $before
	 * @param bool $send
	 */
	public function onSlotChange(int $index, Item $before, bool $send){
		parent::onSlotChange($index, $before, $send);

		$this->getHolder()->scheduleUpdate();
		$this->getHolder()->updateSurface();
	}
	
	public function getNetworkType() : int{
		return WindowTypes::BREWING_STAND;
	}
}