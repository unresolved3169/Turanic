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

namespace pocketmine\entity\boss;

use pocketmine\entity\FlyingAnimal;
use pocketmine\item\Item as ItemItem;

class Wither extends FlyingAnimal {
	const NETWORK_ID = self::WITHER;

	public $width = 0.72;
	public $height = 0;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Wither";
	}

	public function initEntity(){
		$this->setMaxHealth(300);
		parent::initEntity();
	}

	//TODO: Add Spawn Moment and Dead

    /**
     * @return array|ItemItem[]
     * @throws \TypeError
     */
    public function getDrops(){
		$drops = [ItemItem::get(ItemItem::NETHER_STAR, 0, 1)];
		return $drops;
	}

	public function getXpDropAmount(): int{
        return 50;
    }
}
