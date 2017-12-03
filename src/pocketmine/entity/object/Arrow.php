<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\entity\Projectile;
use pocketmine\event\inventory\InventoryPickupArrowEvent;
use pocketmine\item\Potion;
use pocketmine\level\Level;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\MobSpellParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\TakeItemEntityPacket;
use pocketmine\Player;
use pocketmine\item\Item as ItemItem;

class Arrow extends Projectile {
	const NETWORK_ID = 80;

	public $width = 0.5;
	public $length = 0.5;
	public $height = 0.5;

	protected $gravity = 0.05;
	protected $drag = 0.01;

	protected $damage = 2;
	
	protected $sound = true;
	
	protected $isCritical;
	protected $potionId;

	/**
	 * Arrow constructor.
	 *
	 * @param Level       $level
	 * @param CompoundTag $nbt
	 * @param Entity|null $shootingEntity
	 * @param bool        $critical
	 */
	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, $critical = false){
		$this->isCritical = (bool) $critical;
		if(!isset($nbt->Potion)){
			$nbt->Potion = new ShortTag("Potion", 0);
		}
		parent::__construct($level, $nbt, $shootingEntity);
		$this->potionId = $this->namedtag["Potion"];
	}

	/**
	 * @return bool
	 */
	public function isCritical() : bool{
		return $this->isCritical;
	}

	/**
	 * @return int
	 */
	public function getPotionId() : int{
		return $this->potionId;
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);

       if(!$this->hadCollision and $this->isCritical){
			$this->level->addParticle(new CriticalParticle($this->add(
				$this->width / 2 + mt_rand(-100, 100) / 500,
				$this->height / 2 + mt_rand(-100, 100) / 500,
				$this->width / 2 + mt_rand(-100, 100) / 500)));
		}elseif($this->onGround){
			$this->isCritical = false;
			if($this->sound === true and $this->level !== null){ //Prevents error of $this->level returning null
				$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BOW_HIT);
				$this->sound = false;
			}
		}

		if($this->potionId != 0){
			if(!$this->onGround or ($this->onGround and ($currentTick % 4) == 0)){
				$color = Potion::getColor($this->potionId - 1);
				$this->level->addParticle(new MobSpellParticle($this->add(
					$this->width / 2 + mt_rand(-100, 100) / 500,
					$this->height / 2 + mt_rand(-100, 100) / 500,
					$this->width / 2 + mt_rand(-100, 100) / 500), $color[0], $color[1], $color[2]));
			}
			$hasUpdate = true;
		}

		if($this->age > 1200){
			$this->kill();
			$hasUpdate = true;
		}

		$this->timings->stopTiming();

		return $hasUpdate;
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = Arrow::NETWORK_ID;
		$pk->entityRuntimeId = $this->getId();
        $pk->position = $this->getPosition();
        $pk->motion = $this->getMotion();
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	public function onCollideWithPlayer(Player $player): bool{
        if(!$this->hadCollision){
            return false;
        }
        $item = ItemItem::get(ItemItem::ARROW, 0, 1);
        $playerInventory = $player->getInventory();
        if($player->isSurvival() and !$playerInventory->canAddItem($item)){
            return false;
        }
        $this->server->getPluginManager()->callEvent($ev = new InventoryPickupArrowEvent($playerInventory, $this));
        if($ev->isCancelled()){
            return false;
        }
        $pk = new TakeItemEntityPacket();
        $pk->eid = $player->getId();
        $pk->target = $this->getId();
        $this->server->broadcastPacket($this->getViewers(), $pk);
        $playerInventory->addItem(clone $item);
        $this->kill();
        return true;
	}
}
