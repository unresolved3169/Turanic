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

namespace pocketmine\entity\hostile;

use pocketmine\entity\Monster;
use pocketmine\item\Item as ItemItem;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class Vindicator extends Monster {
	const NETWORK_ID = self::VINDICATOR;

	public $width = 0.6;
	public $height = 0;

	/**
	 * @return string
	 */
	public function getName(){
		return "Vindicator";
	}

	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		$this->setMaxHealth(24);
		parent::initEntity();
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		$drops = [
			ItemItem::get(ItemItem::EMERALD, 0, mt_rand(0, 1))
		];

		return $drops;
	}

    public function getXpDropAmount(): int{
        return 5;
    }
}