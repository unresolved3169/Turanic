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

namespace pocketmine\event\entity;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;

/**
 * Called when an entity takes damage from another entity.
 */
class EntityDamageByEntityEvent extends EntityDamageEvent{

	/** @var int */
	private $damagerEntityId;
	/** @var float */
	private $knockBack;

	/**
	 * @param Entity        $damager
	 * @param Entity        $entity
	 * @param int           $cause
	 * @param float|float[] $damage
	 * @param float         $knockBack
	 */
	public function __construct(Entity $damager, Entity $entity, int $cause, $damage, float $knockBack = 0.4){
		$this->damagerEntityId = $damager->getId();
		$this->knockBack = $knockBack;
		parent::__construct($entity, $cause, $damage);
		$this->addAttackerModifiers($damager);
	}

	protected function addAttackerModifiers(Entity $damager){
		if($damager instanceof Living){ //TODO: move this to entity classes
			if($damager->hasEffect(Effect::STRENGTH)){
				$this->setDamage($this->getDamage(self::MODIFIER_BASE) * 0.3 * $damager->getEffect(Effect::STRENGTH)->getEffectLevel(), self::MODIFIER_STRENGTH);
			}

			if($damager->hasEffect(Effect::WEAKNESS)){
				$this->setDamage(-($this->getDamage(self::MODIFIER_BASE) * 0.2 * $damager->getEffect(Effect::WEAKNESS)->getEffectLevel()), self::MODIFIER_WEAKNESS);
			}
		}
	}

	/**
	 * Returns the attacking entity, or null if the attacker has been killed or closed.
	 *
	 * @return Entity|null
	 */
	public function getDamager(){
		return $this->getEntity()->getLevel()->getServer()->findEntity($this->damagerEntityId, $this->getEntity()->getLevel());
	}

	/**
	 * @return float
	 */
	public function getKnockBack() : float{
		return $this->knockBack;
	}

	/**
	 * @param float $knockBack
	 */
	public function setKnockBack(float $knockBack){
		$this->knockBack = $knockBack;
	}
}
