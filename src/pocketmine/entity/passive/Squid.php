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
use pocketmine\entity\WaterAnimal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item as ItemItem;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;

class Squid extends WaterAnimal implements Ageable {
	const NETWORK_ID = self::SQUID;

	public $width = 0.95;
	public $height = 0.95;

	/** @var Vector3 */
	public $swimDirection = null;
	public $swimSpeed = 0.1;

	private $switchDirectionTicker = 0;

	public function initEntity(){
		parent::initEntity();
		$this->setMaxHealth(5);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Squid";
	}

    /**
     * @param EntityDamageEvent $source
     * @return bool|void
     * @internal param float $damage
     */
	public function attack(EntityDamageEvent $source){
		parent::attack($source);
		if($source->isCancelled()){
			return;
		}

		if($source instanceof EntityDamageByEntityEvent){
			$this->swimSpeed = mt_rand(150, 350) / 2000;
			$e = $source->getDamager();
			$this->swimDirection = (new Vector3($this->x - $e->x, $this->y - $e->y, $this->z - $e->z))->normalize();

			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->getId();
			$pk->event = EntityEventPacket::SQUID_INK_CLOUD;
			$this->server->broadcastPacket($this->hasSpawned, $pk);
		}
	}

	/**
	 * @return Vector3
	 */
	private function generateRandomDirection(){
		return new Vector3(mt_rand(-1000, 1000) / 1000, mt_rand(-500, 500) / 1000, mt_rand(-1000, 1000) / 1000);
	}


	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate(int $currentTick){
		if($this->closed !== false){
			return false;
		}

		if(++$this->switchDirectionTicker === 100){
			$this->switchDirectionTicker = 0;
			if(mt_rand(0, 100) < 50){
				$this->swimDirection = null;
			}
		}

		$this->lastUpdate = $currentTick;

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);

		if($this->isAlive()){

			if($this->y > 62 and $this->swimDirection !== null){
				$this->swimDirection->y = -0.5;
			}

			$inWater = $this->isInsideOfWater();
			if(!$inWater){
				$this->motionY -= $this->gravity;
				$this->swimDirection = null;
			}elseif($this->swimDirection !== null){
				if($this->motionX ** 2 + $this->motionY ** 2 + $this->motionZ ** 2 <= $this->swimDirection->lengthSquared()){
					$this->motionX = $this->swimDirection->x * $this->swimSpeed;
					$this->motionY = $this->swimDirection->y * $this->swimSpeed;
					$this->motionZ = $this->swimDirection->z * $this->swimSpeed;
				}
			}else{
				$this->swimDirection = $this->generateRandomDirection();
				$this->swimSpeed = mt_rand(50, 100) / 2000;
			}

			$expectedPos = new Vector3($this->x + $this->motionX, $this->y + $this->motionY, $this->z + $this->motionZ);

			$this->move($this->motionX, $this->motionY, $this->motionZ);

			if($expectedPos->distanceSquared($this) > 0){
				$this->swimDirection = $this->generateRandomDirection();
				$this->swimSpeed = mt_rand(50, 100) / 2000;
			}

			$friction = 1 - $this->drag;

			$this->motionX *= $friction;
			$this->motionY *= 1 - $this->drag;
			$this->motionZ *= $friction;

			$f = sqrt(($this->motionX ** 2) + ($this->motionZ ** 2));
			$this->yaw = (-atan2($this->motionX, $this->motionZ) * 180 / M_PI);
			$this->pitch = (-atan2($f, $this->motionY) * 180 / M_PI);

			if($this->onGround){
				$this->motionY *= -0.5;
			}

		}

		$this->timings->stopTiming();

		return $hasUpdate or !$this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
	}

	/**
	 * @return array
	 */
	public function getDrops(){
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent and $cause->getDamager() instanceof Player){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getItemInHand()->getEnchantmentLevel(Enchantment::LOOTING);

				$drops = [ItemItem::get(ItemItem::DYE, 0, mt_rand(1, 3 + $lootingL))];

				return $drops;
			}
		}

		return [];
	}

    public function getXpDropAmount(): int{
        return !$this->isBaby() ? mt_rand(1,3) : 0;
    }
}
