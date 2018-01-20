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

namespace pocketmine\entity\passive;

use pocketmine\entity\Ageable;
use pocketmine\entity\behavior\{
    LookAtPlayerBehavior, PanicBehavior, RandomLookaroundBehavior, StrollBehavior
};
use pocketmine\entity\Mob;
use pocketmine\entity\NPC;

class Villager extends Mob implements NPC, Ageable {
	
	const NETWORK_ID = self::VILLAGER;

    const PROFESSION_FARMER = 0;
    const PROFESSION_LIBRARIAN = 1;
    const PROFESSION_PRIEST = 2;
    const PROFESSION_BLACKSMITH = 3;
    const PROFESSION_BUTCHER = 4;

	public $width = 0.6;
	public $height = 1.8;

    public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));

		parent::initEntity();

        /** @var int $profession */
        $profession = $this->namedtag->getInt("Profession", self::PROFESSION_FARMER);

        if($profession > 4 or $profession < 0){
            $profession = self::PROFESSION_FARMER;
        }

        $this->setProfession($profession);
	}

	public function saveNBT(){
        parent::saveNBT();
        $this->namedtag->setInt("Profession", $this->getProfession());
    }

    /**
	 * @return string
	 */
	public function getName() : string{
		return "Villager";
	}

    /**
     * Sets the villager profession
     *
     * @param int $profession
     */
    public function setProfession(int $profession){
        $this->propertyManager->setInt(self::DATA_VARIANT, $profession);
    }

    public function getProfession() : int{
        return $this->propertyManager->getInt(self::DATA_VARIANT);
    }

    public function isBaby() : bool{
        return $this->getGenericFlag(self::DATA_FLAG_BABY);
    }

    public function getXpDropAmount(): int{
        return 0;
    }
}
