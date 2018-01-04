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

use pocketmine\entity\Animal;
use pocketmine\entity\behavior\{
    LookAtPlayerBehavior, PanicBehavior, RandomLookaroundBehavior, StrollBehavior
};
use pocketmine\entity\Entity;
use pocketmine\item\Item as ItemItem;

class ElderGuardian extends Animal {
	const NETWORK_ID = self::ELDER_GUARDIAN;

	public $width = 1.45;
	public $height = 0;

	public $dropExp = [5, 5];
	
	public $drag = 0.2;
	public $gravity = 0.3;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Elder Guardian";
	}

	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		$this->setMaxHealth(80);
		$this->setGenericFlag(Entity::DATA_FLAG_ELDER, true);
		parent::initEntity();
	}

    /**
     * @return array|ItemItem[]
     * @throws \TypeError
     */
    public function getDrops(){
		$drops = [
			ItemItem::get(ItemItem::PRISMARINE_CRYSTALS, 0, mt_rand(0, 1))
		];
		$drops[] = ItemItem::get(ItemItem::PRISMARINE_SHARD, 0, mt_rand(0, 2));

		return $drops;
	}
}
