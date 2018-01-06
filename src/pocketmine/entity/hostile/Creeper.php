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

use pocketmine\entity\behavior\{
    LookAtPlayerBehavior, PanicBehavior, RandomLookaroundBehavior, StrollBehavior
};
use pocketmine\entity\Monster;
use pocketmine\entity\object\Lightning;
use pocketmine\event\entity\CreeperPowerEvent;

class Creeper extends Monster {
	const NETWORK_ID = self::CREEPER;
	const DATA_SWELL = 19;
	const DATA_SWELL_OLD = 20;
	const DATA_SWELL_DIRECTION = 21;
	
	public function initEntity(){
		$this->addBehavior(new PanicBehavior($this, 0.25, 2.0));
		$this->addBehavior(new StrollBehavior($this));
		$this->addBehavior(new LookAtPlayerBehavior($this));
		$this->addBehavior(new RandomLookaroundBehavior($this));
		
        $this->setMaxHealth(20);
		parent::initEntity();
		if(!$this->namedtag->hasTag("powered")){
			$this->setPowered(false);
		}
		$this->setGenericFlag(self::DATA_FLAG_POWERED, $this->isPowered());
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Creeper";
	}
	/**
	 * @param bool           $powered
	 * @param Lightning|null $lightning
	 */
	public function setPowered(bool $powered, Lightning $lightning = null){
		if($lightning != null){
			$powered = true;
			$cause = CreeperPowerEvent::CAUSE_LIGHTNING;
		}else $cause = $powered ? CreeperPowerEvent::CAUSE_SET_ON : CreeperPowerEvent::CAUSE_SET_OFF;

		$this->getLevel()->getServer()->getPluginManager()->callEvent($ev = new CreeperPowerEvent($this, $lightning, $cause));

		if(!$ev->isCancelled()){
			$this->namedtag->setByte("powered", (int) $powered);
			$this->setGenericFlag( self::DATA_FLAG_POWERED, $powered);
		}
	}

	/**
	 * @return bool
	 */
	public function isPowered() : bool{
		return (bool) $this->namedtag["powered"];
	}

    public function getXpDropAmount(): int{
        return 5;
    }
}