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

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\entity\Explosive;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\level\Explosion;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class PrimedTNT extends Entity implements Explosive {
	const NETWORK_ID = self::TNT;

	public $width = 0.98;
	public $height = 0.98;

	protected $gravity = 0.04;
	protected $drag = 0.02;

	protected $fuse;

	public $canCollide = false;

	private $dropItem = true;

	public function __construct(Level $level, CompoundTag $nbt, bool $dropItem = true){
		parent::__construct($level, $nbt);
		$this->dropItem = $dropItem;
	}

	public function attack(EntityDamageEvent $source){
		if($source->getCause() === EntityDamageEvent::CAUSE_VOID){
			parent::attack($source);
		}
	}

	protected function initEntity(){
		parent::initEntity();

        if($this->namedtag->hasTag("Fuse", ShortTag::class)){
            $this->fuse = $this->namedtag->getShort("Fuse");
        }else{
            $this->fuse = 80;
        }

        $this->setGenericFlag(self::DATA_FLAG_IGNITED, true);
        $this->setDataProperty(self::DATA_FUSE_LENGTH, self::DATA_TYPE_INT, $this->fuse);

        $this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_SOUND_IGNITE);
	}

	public function canCollideWith(Entity $entity){
		return false;
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->getShort("Fuse", $this->fuse, true); //older versions incorrectly saved this as a byte
	}

    public function entityBaseTick(int $tickDiff = 1) : bool{
        if($this->closed){
            return false;
        }

        $hasUpdate = parent::entityBaseTick($tickDiff);

        if($this->fuse % 5 === 0){ //don't spam it every tick, it's not necessary
            $this->setDataProperty(self::DATA_FUSE_LENGTH, self::DATA_TYPE_INT, $this->fuse);
        }

        if(!$this->isFlaggedForDespawn()){
            $this->fuse -= $tickDiff;

            if($this->fuse <= 0){
                $this->flagForDespawn();
                $this->explode();
            }
        }

        return $hasUpdate or $this->fuse >= 0;
    }

	public function explode(){
		$this->server->getPluginManager()->callEvent($ev = new ExplosionPrimeEvent($this, 4, $this->dropItem));

		if(!$ev->isCancelled()){
			$explosion = new Explosion($this, $ev->getForce(), $this, $ev->dropItem());
			if($ev->isBlockBreaking()){
				$explosion->explodeA();
			}
			$explosion->explodeB();
		}
	}
}