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

use pocketmine\entity\Entity;
use pocketmine\entity\Monster;
use pocketmine\item\Item as ItemItem;
use pocketmine\entity\behavior\{StrollBehavior, RandomLookaroundBehavior, LookAtPlayerBehavior, PanicBehavior};

class Shulker extends Monster {
	const NETWORK_ID = self::SHULKER;

	public $width = 0.5;
	public $height = 0;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Shulker";
	}

	public function initEntity(){
	    $this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
	    $this->addBehavior(new StrollBehavior($this));
	    $this->addBehavior(new LookAtPlayerBehavior($this));
	    $this->addBehavior(new RandomLookaroundBehavior($this));
		$this->setMaxHealth(30);
		$this->propertyManager->setInt(Entity::DATA_VARIANT, 10);
		parent::initEntity();
	}

    /**
     * @return array|ItemItem[]
     * @throws \TypeError
     */
    public function getDrops(){
		$drops = [
			ItemItem::get(ItemItem::SHULKER_SHELL, 0, mt_rand(0, 1))
		];

		return $drops;
	}

    public function getXpDropAmount(): int{
        return 5;
    }
}