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

namespace pocketmine\item;

class ChainBoots extends Armor {
	/**
	 * ChainBoots constructor.
	 *
	 * @param int $meta
	 */
	public function __construct(int $meta = 0){
		parent::__construct(self::CHAIN_BOOTS, $meta, "Chainmail Boots");
	}

	/**
	 * @return int
	 */
	public function getArmorTier(){
		return Armor::TIER_CHAIN;
	}

	/**
	 * @return int
	 */
	public function getArmorType(){
		return Armor::TYPE_BOOTS;
	}

	/**
	 * @return int
	 */
	public function getMaxDurability(){
		return 196;
	}

	/**
	 * @return int
	 */
	public function getDefensePoints() : int{
		return 1;
	}

	/**
	 * @return bool
	 */
	public function isBoots(){
		return true;
	}
}